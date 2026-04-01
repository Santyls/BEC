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

    /**
     * GET /admin/reportes/datos?tipo=donaciones
     * Devuelve JSON con los datos y estadísticas — usado por el modal de Vista Previa.
     */
    public function datos(Request $request)
    {
        $tipo = $request->input('tipo');

        if (!in_array($tipo, ['donaciones', 'voluntariados'])) {
            return response()->json(['error' => 'Tipo de reporte inválido.'], 422);
        }

        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/{$tipo}/");

        if (!$response->successful()) {
            return response()->json([
                'error' => "Error al obtener datos de {$tipo}. HTTP " . $response->status()
            ], 502);
        }

        $raw   = $response->json();
        $datos = is_array($raw) && array_is_list($raw)
            ? $raw
            : ($raw[$tipo] ?? $raw['data'] ?? $raw['results'] ?? []);

        return response()->json([
            'datos' => $datos,
            'stats' => $this->buildStats($tipo, $datos),
            'total' => count($datos),
        ]);
    }

    /**
     * POST /admin/reportes/generar
     * Genera y descarga el PDF — solo para la acción "download".
     */
    public function generar(Request $request)
    {
        ini_set('memory_limit', '512M');

        $tipo   = $request->input('tipo_reporte');
        $action = $request->input('action', 'download');

        if (!in_array($tipo, ['donaciones', 'voluntariados'])) {
            return response()->json(['error' => 'Tipo de reporte inválido.'], 422);
        }

        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/{$tipo}/");

        if (!$response->successful()) {
            return response()->json([
                'error' => "Error al obtener datos de {$tipo}. HTTP " . $response->status()
            ], 502);
        }

        $raw   = $response->json();
        $datos = is_array($raw) && array_is_list($raw)
            ? $raw
            : ($raw[$tipo] ?? $raw['data'] ?? $raw['results'] ?? []);

        // Limitar a 200 registros para evitar OOM en dompdf
        $datos = array_slice($datos, 0, 200);
        $stats = $this->buildStats($tipo, $datos);

        $pdf = Pdf::loadView('admin.pdf.plantilla_reporte', [
            'tipo'  => $tipo,
            'datos' => $datos,
            'stats' => $stats,
            'fecha' => now()->format('d/m/Y h:i A'),
        ])->setPaper('a4', 'portrait');

        $filename = "reporte_{$tipo}_" . now()->format('Y_m_d') . ".pdf";

        return $action === 'preview'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    private function buildStats(string $tipo, array $datos): array
    {
        if ($tipo === 'donaciones') {
            $porCondicion  = [];
            $cantidadTotal = 0;
            foreach ($datos as $d) {
                $cond = $d['Id_Condicion'] ?? 'Desconocida';
                $porCondicion[$cond] = ($porCondicion[$cond] ?? 0) + 1;
                $cantidadTotal += (float)($d['Cantidad'] ?? 0);
            }
            return [
                'total'          => count($datos),
                'cantidad_total' => $cantidadTotal,
                'por_condicion'  => $porCondicion,
            ];
        }

        if ($tipo === 'voluntariados') {
            $activos = count(array_filter(
                $datos,
                fn($v) => strtolower($v['Estado_Voluntariado'] ?? '') === 'activo'
            ));
            return [
                'total'     => count($datos),
                'activos'   => $activos,
                'inactivos' => count($datos) - $activos,
            ];
        }

        return ['total' => count($datos)];
    }
}
