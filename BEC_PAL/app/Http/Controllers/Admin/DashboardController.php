<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index()
    {
        $albergues = $this->api->get('/albergues/');
        $campanas = $this->api->get('/campanas/');
        $voluntariados = $this->api->get('/voluntariados/');
        $donaciones = $this->api->get('/donaciones/');

        $inicioMes = Carbon::now()->startOfMonth();

        $totalAlbergues = count($albergues);
        $campanasActivas = count(array_filter($campanas, fn ($c) => $c['estado_id'] == 2));
        $voluntariadosActivos = count(array_filter($voluntariados, fn ($v) => in_array($v['estado_id'], [1, 2])));
        $donacionesMes = count(array_filter(
            $donaciones,
            fn ($d) => Carbon::parse($d['fecha_donacion'])->gte($inicioMes)
        ));

        $recientes = collect([
            ...array_map(fn ($d) => [
                'texto' => 'Donación registrada para '.($d['albergue']['nombre'] ?? 'un albergue').'.',
                'fecha' => $d['fecha_donacion'],
                'color' => 'bg-emerald-500',
            ], array_slice($donaciones, 0, 3)),
            ...array_map(fn ($v) => [
                'texto' => 'Voluntariado "'.$v['nombre_programa'].'" programado.',
                'fecha' => $v['fecha_programada'],
                'color' => 'bg-blue-500',
            ], array_slice($voluntariados, 0, 3)),
        ])->sortByDesc('fecha')->take(5)->values();

        return view('admin.dashboard', compact(
            'totalAlbergues', 'campanasActivas', 'voluntariadosActivos', 'donacionesMes', 'recientes'
        ));
    }
}
