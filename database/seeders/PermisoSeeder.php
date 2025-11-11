<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'ver_materias',
            'ver_horarios',
            'gestionar_asistencias',
            'ver_aulas',
            'ver_reportes_personal',
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(['nombre' => $permiso]);
        }
    }
}