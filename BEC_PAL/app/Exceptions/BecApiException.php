<?php

namespace App\Exceptions;

use Exception;

class BecApiException extends Exception
{
    public int $status;
    public array $errores;

    public function __construct(int $status, array $errores = [], string $message = '')
    {
        $this->status = $status;
        $this->errores = $errores;
        parent::__construct($message ?: ($errores['detail'] ?? 'Error al comunicarse con BEC_API'));
    }

    /** Mensaje listo para mostrar en un formulario, aunque venga como lista de errores de Pydantic. */
    public function mensajeUsuario(): string
    {
        $detail = $this->errores['detail'] ?? null;

        if (is_string($detail)) {
            return $detail;
        }

        if (is_array($detail)) {
            $partes = array_map(
                fn ($e) => is_array($e) ? ($e['msg'] ?? json_encode($e)) : (string) $e,
                $detail
            );
            return implode(' ', $partes);
        }

        return 'Ocurrió un error al comunicarse con la API.';
    }
}
