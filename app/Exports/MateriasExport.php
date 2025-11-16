<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MateriasExport implements FromCollection, WithHeadings
{
    protected $materias;

    public function __construct($materias)
    {
        $this->materias = $materias;
    }

    public function collection()
    {
        return $this->materias->map(function($m){
            return [
                'ID' => $m->sigla,
                'Nombre' => $m->nombre,
                'Semestre' => $m->semestre,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Semestre'];
    }
}
