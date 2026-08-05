<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BecApiException;
use App\Http\Controllers\Controller;
use App\Services\BecApiClient;
use App\Support\Paginador;
use Illuminate\Http\Request;

class VoluntariadoController extends Controller
{
    public function __construct(private BecApiClient $api) {}

    public function index(Request $request)
    {
        $voluntariados = $this->api->get('/voluntariados/');
        $albergues = collect($this->api->get('/albergues/', ['solo_activos' => false]))->keyBy('id');
        $estados = collect($this->api->get('/catalogos/estados-voluntariado'))->keyBy('id');

        $busqueda = trim((string) $request->query('q', ''));
        if ($busqueda !== '') {
            $texto = mb_strtolower($busqueda);
            $voluntariados = array_values(array_filter($voluntariados, fn ($v) =>
                str_contains(mb_strtolower($v['nombre_programa']), $texto)
            ));
        }

        $voluntariados = Paginador::paginar($voluntariados);

        return view('admin.voluntariados.index', compact('voluntariados', 'albergues', 'estados', 'busqueda'));
    }

    public function create()
    {
        $albergues = $this->api->get('/albergues/');
        $campanas = $this->api->get('/campanas/');
        return view('admin.voluntariados.create', compact('albergues', 'campanas'));
    }

    public function show(int $id)
    {
        $voluntariado = $this->api->get("/voluntariados/{$id}");
        $inscritos = $this->api->get("/voluntariados/{$id}/inscritos");
        $albergue = $voluntariado['albergue_id'] ? $this->api->get("/albergues/{$voluntariado['albergue_id']}") : null;
        $campana = $voluntariado['campana_id'] ? $this->api->get("/campanas/{$voluntariado['campana_id']}") : null;
        $estados = collect($this->api->get('/catalogos/estados-voluntariado'))->keyBy('id');
        $estadosInscripcion = collect($this->api->get('/catalogos/estados-inscripcion'))->keyBy('id');

        return view('admin.voluntariados.show', compact(
            'voluntariado', 'inscritos', 'albergue', 'campana', 'estados', 'estadosInscripcion'
        ));
    }

    public function cancelarInscripcion(int $id, int $inscripcionId)
    {
        try {
            $this->api->put("/inscripciones/{$inscripcionId}/cancelar");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Inscripción cancelada correctamente.');
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);

        try {
            $this->api->post('/voluntariados/', $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.voluntariados.index')->with('exito', 'Voluntariado registrado correctamente.');
    }

    public function edit(int $id)
    {
        $voluntariado = $this->api->get("/voluntariados/{$id}");
        $albergues = $this->api->get('/albergues/');
        $campanas = $this->api->get('/campanas/');
        $estados = $this->api->get('/catalogos/estados-voluntariado');
        return view('admin.voluntariados.edit', compact('voluntariado', 'albergues', 'campanas', 'estados'));
    }

    public function update(Request $request, int $id)
    {
        $datos = $this->validarDatos($request, esEdicion: true);

        try {
            $this->api->put("/voluntariados/{$id}", $datos);
        } catch (BecApiException $e) {
            return back()->withInput()->with('error', $e->mensajeUsuario());
        }

        return redirect()->route('admin.voluntariados.index')->with('exito', 'Voluntariado actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        try {
            $this->api->delete("/voluntariados/{$id}");
        } catch (BecApiException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Voluntariado cancelado correctamente.');
    }

    private function validarDatos(Request $request, bool $esEdicion = false): array
    {
        $reglas = [
            'nombre_programa' => 'required|string|min:3|max:150',
            'albergue_id' => 'nullable|integer',
            'campana_id' => 'nullable|integer',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_programada' => 'required|date',
            'cupo_maximo' => 'nullable|integer|min:1',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'descripcion_requisitos' => 'required|string',
        ];

        // El estado solo se edita explícitamente después de creado — al crear,
        // el backend siempre lo inicializa como "Programado" (ver VoluntariadoCreate en la API).
        if ($esEdicion) {
            $reglas['estado_id'] = 'required|integer';
        }

        $datos = $request->validate($reglas, [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // Campos "nullable" que además pueden llegar como "" (select vacío) o
        // no llegar en absoluto (checkbox/select sin seleccionar): en ambos
        // casos deben quedar en null, no en una key inexistente ni en "".
        $datos['albergue_id'] = empty($datos['albergue_id']) ? null : $datos['albergue_id'];
        $datos['campana_id'] = empty($datos['campana_id']) ? null : $datos['campana_id'];
        $datos['cupo_maximo'] = empty($datos['cupo_maximo']) ? null : $datos['cupo_maximo'];
        $datos['ubicacion'] = $datos['ubicacion'] ?? null;

        return $datos;
    }
}
