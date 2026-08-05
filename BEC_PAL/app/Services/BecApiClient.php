<?php

namespace App\Services;

use App\Exceptions\BecApiException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * Único punto de contacto de BEC_PAL con BEC_API. Toda la data de negocio
 * (usuarios, albergues, campañas, voluntariados, donaciones) pasa por aquí —
 * BEC_PAL no tiene Eloquent propio para esas entidades a propósito, para no
 * duplicar validaciones/reglas de negocio que ya viven en la API.
 */
class BecApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.bec_api.base_url'), '/');
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, ['query' => $query]);
    }

    public function post(string $path, array $body = []): array
    {
        return $this->request('post', $path, ['json' => $body]);
    }

    public function put(string $path, array $body = []): array
    {
        return $this->request('put', $path, ['json' => $body]);
    }

    public function delete(string $path): void
    {
        $this->request('delete', $path);
    }

    public function postForm(string $path, array $form = []): array
    {
        return $this->request('post', $path, ['form_params' => $form]);
    }

    public function postFile(string $path, string $campo, \Illuminate\Http\UploadedFile $archivo): array
    {
        return $this->request('post', $path, [
            'attach' => [$campo, $archivo->get(), $archivo->getClientOriginalName()],
        ]);
    }

    /**
     * Login/registro no llevan token todavía — se llaman directo, sin pasar
     * por el mecanismo de refresh (no tiene sentido refrescar algo que aún
     * no existe).
     */
    public function sinAuth(): self
    {
        return $this;
    }

    private function request(string $method, string $path, array $options = [], bool $reintentado = false): array
    {
        // timeout explícito: sin esto, una llamada que se cuelga (red, BEC_API caído a
        // medias, etc.) puede tardar casi un minuto en fallar por defaults de bajo nivel
        // de Guzzle/TCP, dejando al usuario viendo una pantalla en blanco sin explicación.
        $pending = Http::baseUrl($this->baseUrl)->acceptJson()->timeout(10)->connectTimeout(5);

        $token = Session::get('bec_access_token');
        if ($token) {
            $pending = $pending->withToken($token);
        }

        $query = $options['query'] ?? [];
        $body = $options['json'] ?? null;
        $form = $options['form_params'] ?? null;
        $attach = $options['attach'] ?? null;

        try {
            $response = match (true) {
                $attach !== null => $pending->attach(...$attach)->{$method}($path),
                $form !== null => $pending->asForm()->{$method}($path, $form),
                $body !== null => $pending->{$method}($path, $body),
                default => $pending->{$method}($path, $query),
            };
        } catch (\Throwable $e) {
            // Cubre timeouts, conexión rechazada, y errores de serialización (p. ej. texto
            // con bytes UTF-8 inválidos) — nunca debe llegar como 500 crudo al usuario.
            throw new BecApiException(0, [], 'No se pudo comunicar con el servidor. Intenta de nuevo.');
        }

        if ($response->status() === 401 && $token && !$reintentado) {
            if ($this->refrescarToken()) {
                return $this->request($method, $path, $options, reintentado: true);
            }
        }

        return $this->manejarRespuesta($response);
    }

    private function refrescarToken(): bool
    {
        $refreshToken = Session::get('bec_refresh_token');
        if (!$refreshToken) {
            return false;
        }

        $response = Http::baseUrl($this->baseUrl)->acceptJson()
            ->post('/auth/refresh', ['refresh_token' => $refreshToken]);

        if (!$response->successful()) {
            Session::forget(['bec_access_token', 'bec_refresh_token', 'bec_user']);
            return false;
        }

        $datos = $response->json();
        Session::put('bec_access_token', $datos['access_token']);
        Session::put('bec_refresh_token', $datos['refresh_token']);
        return true;
    }

    private function manejarRespuesta(Response $response): array
    {
        if ($response->status() === 204 || $response->body() === '') {
            if (!$response->successful()) {
                throw new BecApiException($response->status());
            }
            return [];
        }

        $json = $response->json() ?? [];

        if (!$response->successful()) {
            throw new BecApiException($response->status(), $json);
        }

        return $json;
    }
}
