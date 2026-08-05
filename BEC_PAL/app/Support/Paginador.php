<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * BEC_PAL no usa Eloquent (los datos vienen de BEC_API como arrays), así que la
 * paginación de Laravel (pensada para queries) no aplica directo — este helper
 * envuelve un array ya filtrado/buscado en un LengthAwarePaginator manual,
 * conservando los demás parámetros de la URL (?q=, ?categoria_id=, etc.).
 */
class Paginador
{
    public static function paginar(array $items, int $porPagina = 15): LengthAwarePaginator
    {
        $coleccion = collect(array_values($items));
        $ultimaPagina = max(1, (int) ceil($coleccion->count() / $porPagina));

        // Sin este clamp, un ?page= fuera de rango (bookmark viejo, edición manual de
        // la URL) deja la página "actual" apuntando a un hueco vacío: firstItem()/
        // lastItem() devuelven null y el texto "Mostrando – de N" queda roto.
        $pagina = min(max(1, LengthAwarePaginator::resolveCurrentPage()), $ultimaPagina);
        $items_pagina = $coleccion->forPage($pagina, $porPagina)->values();

        return new LengthAwarePaginator(
            $items_pagina,
            $coleccion->count(),
            $porPagina,
            $pagina,
            ['path' => Request::url(), 'query' => Request::query()]
        );
    }
}
