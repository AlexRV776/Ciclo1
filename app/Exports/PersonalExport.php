<?php

namespace App\Exports;

use App\Models\Usuario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonalExport implements FromCollection, WithHeadings, WithMapping
{
    protected $personal;

    public function __construct($personal)
    {
        $this->personal = $personal;
    }

    public function collection()
    {
        return $this->personal;
    }

    public function headings(): array
    {
        return [
            'Registro',
            'Nombre',
            'Correo',
            'Rol',
            'Fecha de Contrato',
            'Sueldo'
        ];
    }

    public function map($usuario): array
    {
        return [
            $usuario->registro,
            $usuario->nombre,
            $usuario->correo,
            $usuario->rol->nombre ?? 'Sin rol',
            $usuario->docente->fecha_contrato ?? 'No registrado',
            $usuario->docente->sueldo ?? 'No registrado',
        ];
    }
}
