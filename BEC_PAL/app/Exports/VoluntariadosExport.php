<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VoluntariadosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $voluntariados) {}

    public function collection(): Collection
    {
        return collect($this->voluntariados);
    }

    public function headings(): array
    {
        return ['Actividad', 'Albergue', 'Campaña', 'Fecha', 'Hora inicio', 'Hora fin', 'Ubicación', 'Cupo máximo', 'Inscritos', 'Estado'];
    }

    public function map($voluntariado): array
    {
        $estados = [1 => 'Programado', 2 => 'Activo', 3 => 'Finalizado', 4 => 'Cancelado'];

        return [
            $voluntariado['nombre_programa'],
            $voluntariado['albergue_nombre'] ?? '—',
            $voluntariado['campana_nombre'] ?? '—',
            $voluntariado['fecha_programada'],
            substr($voluntariado['hora_inicio'], 0, 5),
            substr($voluntariado['hora_fin'], 0, 5),
            $voluntariado['ubicacion'] ?? '—',
            $voluntariado['cupo_maximo'] ?? 'Sin límite',
            $voluntariado['inscritos'],
            $estados[$voluntariado['estado_id']] ?? '—',
        ];
    }
}
