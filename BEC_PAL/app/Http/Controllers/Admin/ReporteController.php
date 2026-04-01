<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('BEC_API_URL', 'http://bec_api_app:5000'), '/');
    }

    public function index()
    {
        return view('admin.reportes.index');
    }

    public function generar(Request $request)
    {
        ini_set('memory_limit', '512M');

        $tipo_reporte = $request->input('tipo_reporte');
        $action       = $request->input('action', 'preview');

        if (!in_array($tipo_reporte, ['donaciones', 'voluntariados'])) {
            return back()->with('error', 'Tipo de reporte inválido.');
        }

        $endpoint = "{$this->apiUrl}/{$tipo_reporte}/";
        $response = Http::withToken(session('api_token'))->get($endpoint);

        if (!$response->successful()) {
            return back()->with('error',
                "No se pudo conectar con la API para obtener datos de {$tipo_reporte}. HTTP Status: " . $response->status()
            );
        }

        $raw = $response->json();

        // Normalizar: admite respuesta como lista directa, o envuelta en clave
        if (is_array($raw) && array_is_list($raw)) {
            $datos = $raw;
        } else {
            $datos = $raw[$tipo_reporte] ?? $raw['data'] ?? $raw['results'] ?? [];
        }

        $stats = $this->buildStats($tipo_reporte, $datos);

        $pdf = Pdf::loadView('admin.pdf.plantilla_reporte', [
            'tipo'  => $tipo_reporte,
            'datos' => $datos,
            'stats' => $stats,
            'fecha' => now()->format('d/m/Y h:i A'),
        ])->setPaper('a4', 'portrait');

        $filename = "reporte_{$tipo_reporte}_" . now()->format('Y_m_d') . ".pdf";

        return $action === 'download'
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }

    private function buildStats(string $tipo, array $datos): array
    {
        if ($tipo === 'donaciones') {
            $total       = count($datos);
            $porCondicion = [];
            $cantidadTotal = 0;

            foreach ($datos as $d) {
                $cond = $d['Id_Condicion'] ?? 'Desconocida';
                $porCondicion[$cond] = ($porCondicion[$cond] ?? 0) + 1;
                $cantidadTotal += (float)($d['Cantidad'] ?? 0);
            }

            return [
                'total'          => $total,
                'cantidad_total' => $cantidadTotal,
                'por_condicion'  => $porCondicion,
            ];
        }

        if ($tipo === 'voluntariados') {
            $total    = count($datos);
            $activos  = count(array_filter($datos, fn($v) => strtolower($v['Estado_Voluntariado'] ?? '') === 'activo'));

            return [
                'total'     => $total,
                'activos'   => $activos,
                'inactivos' => $total - $activos,
            ];
        }

        return ['total' => count($datos)];
    }
}
