<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsistenciaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $asistencias;

    public function __construct($asistencias)
    {
        $this->asistencias = $asistencias;
    }

    public function collection()
    {
        return $this->asistencias;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Docente',
            'Materia',
            'Estado',
            'Modalidad',
        ];
    }

    public function map($a): array
    {
        return [
            $a->fecha,
            $a->docente->usuario->nombre ?? 'No registrado',
            $a->horarioMateria->grupoMateria->materia->nombre ?? 'No asignada',
            ucfirst($a->estado),
            ucfirst($a->modalidad),
        ];
    }
}

