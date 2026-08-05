<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DonacionesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $donaciones) {}

    public function collection(): Collection
    {
        return collect($this->donaciones);
    }

    public function headings(): array
    {
        return ['Folio', 'Fecha', 'Donante', 'Correo', 'Categoría', 'Condición', 'Cantidad', 'Marca', 'Albergue destino'];
    }

    public function map($donacion): array
    {
        return [
            'DON-'.str_pad((string) $donacion['id'], 3, '0', STR_PAD_LEFT),
            $donacion['fecha_donacion'],
            $donacion['usuario']
                ? trim($donacion['usuario']['nombre'].' '.$donacion['usuario']['apellido_paterno'])
                : 'Anónimo',
            $donacion['usuario']['correo'] ?? '—',
            $donacion['categoria']['nombre'] ?? '—',
            $donacion['condicion']['nombre'] ?? '—',
            $donacion['cantidad'],
            $donacion['marca'] ?? '—',
            $donacion['albergue']['nombre'] ?? '—',
        ];
    }
}
