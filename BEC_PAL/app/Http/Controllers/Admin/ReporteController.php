<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DonacionesExport;
use App\Exports\VoluntariadosExport;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index(Request $request)
    {
        [$desde, $hasta] = $this->rangoFechas($request);

        $albergues = $this->api->get('/albergues/', ['solo_activos' => false]);
        $campanas = $this->api->get('/campanas/');
        $catalogoVoluntariados = $this->api->get('/voluntariados/');

        $albergueId = $request->query('donacion_albergue_id');
        $campanaId = $request->query('campana_id');
        $voluntariadoId = $request->query('voluntariado_id');

        $reporteDonaciones = $this->calcularReporteDonaciones($desde, $hasta, $albergueId);
        $reporteVoluntariados = $this->calcularReporteVoluntariados($desde, $hasta, $campanaId, $voluntariadoId);

        return view('admin.reportes.index', array_merge(
            [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
                'albergues' => $albergues,
                'campanas' => $campanas,
                'catalogoVoluntariados' => $catalogoVoluntariados,
                'albergueSeleccionado' => $albergueId,
                'campanaSeleccionada' => $campanaId,
                'voluntariadoSeleccionado' => $voluntariadoId,
            ],
            $reporteDonaciones,
            $reporteVoluntariados
        ));
    }

    public function exportarDonacionesXlsx(Request $request)
    {
        [$desde, $hasta] = $this->rangoFechas($request);
        $filas = $this->donacionesEnRango($desde, $hasta, $request->query('donacion_albergue_id'));

        return Excel::download(new DonacionesExport($filas), 'reporte-donaciones.xlsx');
    }

    public function exportarDonacionesPdf(Request $request)
    {
        [$desde, $hasta] = $this->rangoFechas($request);
        $filas = $this->donacionesEnRango($desde, $hasta, $request->query('donacion_albergue_id'));

        $pdf = Pdf::loadView('admin.reportes.pdf.donaciones', [
            'filas' => $filas,
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
        ]);

        return $pdf->download('reporte-donaciones.pdf');
    }

    public function exportarVoluntariadosXlsx(Request $request)
    {
        [$desde, $hasta] = $this->rangoFechas($request);
        $filas = $this->voluntariadosConNombres($desde, $hasta, $request->query('campana_id'), $request->query('voluntariado_id'));

        return Excel::download(new VoluntariadosExport($filas), 'reporte-voluntariados.xlsx');
    }

    public function exportarVoluntariadosPdf(Request $request)
    {
        [$desde, $hasta] = $this->rangoFechas($request);
        $filas = $this->voluntariadosConNombres($desde, $hasta, $request->query('campana_id'), $request->query('voluntariado_id'));

        $pdf = Pdf::loadView('admin.reportes.pdf.voluntariados', [
            'filas' => $filas,
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
        ]);

        return $pdf->download('reporte-voluntariados.pdf');
    }

    /** Igual que voluntariadosEnRango pero con albergue_nombre/campana_nombre ya resueltos, para exportar. */
    private function voluntariadosConNombres(Carbon $desde, Carbon $hasta, ?string $campanaId, ?string $voluntariadoId): array
    {
        $albergues = collect($this->api->get('/albergues/', ['solo_activos' => false]))->keyBy('id');
        $campanas = collect($this->api->get('/campanas/'))->keyBy('id');

        $filas = $this->voluntariadosEnRango($desde, $hasta, $campanaId, $voluntariadoId);

        return array_map(function ($v) use ($albergues, $campanas) {
            $v['albergue_nombre'] = $v['albergue_id'] ? ($albergues[$v['albergue_id']]['nombre'] ?? '—') : '—';
            $v['campana_nombre'] = $v['campana_id'] ? ($campanas[$v['campana_id']]['nombre'] ?? '—') : '—';
            return $v;
        }, $filas);
    }

    /**
     * Rango por defecto: del primer día del mes actual a hoy. El usuario puede
     * ampliarlo/reducirlo desde el formulario de filtros (?desde=&hasta=).
     */
    private function rangoFechas(Request $request): array
    {
        $desde = $request->query('desde')
            ? Carbon::parse($request->query('desde'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $hasta = $request->query('hasta')
            ? Carbon::parse($request->query('hasta'))->endOfDay()
            : Carbon::now()->endOfDay();

        return [$desde, $hasta];
    }

    private function donacionesEnRango(Carbon $desde, Carbon $hasta, ?string $albergueId): array
    {
        $filtros = [];
        if (!empty($albergueId)) {
            $filtros['albergue_id'] = $albergueId;
        }

        $donaciones = $this->api->get('/donaciones/', $filtros);

        return array_values(array_filter(
            $donaciones,
            fn ($d) => Carbon::parse($d['fecha_donacion'])->between($desde, $hasta)
        ));
    }

    private function voluntariadosEnRango(Carbon $desde, Carbon $hasta, ?string $campanaId, ?string $voluntariadoId): array
    {
        $voluntariados = $this->api->get('/voluntariados/');

        return array_values(array_filter($voluntariados, function ($v) use ($desde, $hasta, $campanaId, $voluntariadoId) {
            if (!Carbon::parse($v['fecha_programada'])->between($desde, $hasta)) {
                return false;
            }
            if (!empty($campanaId) && (string) $v['campana_id'] !== (string) $campanaId) {
                return false;
            }
            if (!empty($voluntariadoId) && (string) $v['id'] !== (string) $voluntariadoId) {
                return false;
            }
            return true;
        }));
    }

    private function calcularReporteDonaciones(Carbon $desde, Carbon $hasta, ?string $albergueId): array
    {
        $categorias = collect($this->api->get('/catalogos/categorias'))->keyBy('id');
        $usuarios = collect($this->api->get('/usuarios/', ['limit' => 200]))->keyBy('id');

        $donacionesPeriodo = $this->donacionesEnRango($desde, $hasta, $albergueId);
        $totalDonacionesPeriodo = count($donacionesPeriodo);

        $nombreDonador = function (?array $filas) use ($usuarios) {
            $topId = collect($filas)->pluck('usuario_id')->filter()->countBy()->sortDesc()->keys()->first();
            return $topId && $usuarios->has($topId)
                ? trim($usuarios[$topId]['nombre'].' '.$usuarios[$topId]['apellido_paterno'])
                : 'Sin donadores registrados';
        };

        $topDonadorPeriodo = $nombreDonador($donacionesPeriodo);

        // "Top donador en general": respeta el filtro de albergue pero ignora el
        // rango de fechas — es una vista histórica completa, no del periodo.
        $filtrosGeneral = [];
        if (!empty($albergueId)) {
            $filtrosGeneral['albergue_id'] = $albergueId;
        }
        $donacionesGeneral = $this->api->get('/donaciones/', $filtrosGeneral);
        $topDonadorGeneral = $nombreDonador($donacionesGeneral);

        $categoriaTopId = collect($donacionesPeriodo)->countBy('categoria_id')->sortDesc()->keys()->first();
        $categoriaTop = $categoriaTopId && $categorias->has($categoriaTopId)
            ? $categorias[$categoriaTopId]['nombre']
            : 'Sin datos';

        return [
            'totalDonacionesPeriodo' => $totalDonacionesPeriodo,
            'topDonadorPeriodo' => $topDonadorPeriodo,
            'topDonadorGeneral' => $topDonadorGeneral,
            'categoriaTop' => $categoriaTop,
        ];
    }

    private function calcularReporteVoluntariados(Carbon $desde, Carbon $hasta, ?string $campanaId, ?string $voluntariadoId): array
    {
        $campanas = collect($this->api->get('/campanas/'))->keyBy('id');

        $voluntariadosPeriodo = $this->voluntariadosEnRango($desde, $hasta, $campanaId, $voluntariadoId);
        $totalVoluntariadosPeriodo = count($voluntariadosPeriodo);
        $totalInscritosPeriodo = array_sum(array_column($voluntariadosPeriodo, 'inscritos'));

        $campanaTopId = collect($voluntariadosPeriodo)
            ->pluck('campana_id')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();
        $campanaTop = $campanaTopId && $campanas->has($campanaTopId)
            ? $campanas[$campanaTopId]['nombre']
            : 'Sin campaña asociada';

        return [
            'totalVoluntariadosPeriodo' => $totalVoluntariadosPeriodo,
            'totalInscritosPeriodo' => $totalInscritosPeriodo,
            'campanaTop' => $campanaTop,
        ];
    }
}
