<?php

namespace App\Exports;

use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GruposExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Grupo::select('id', 'nombre')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre del Grupo',
        ];
    }
}
