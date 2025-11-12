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
            'ver_grupos',
            'ver_aulas',
            'ver_bitacora',
            'ver_horarios',
            'ver_reservas',
            'ver_docentes',
            'ver_asignar_materia_grupo',
            'ver_asignar_aula_horario',
            'gestionar_roles',
            'gestionar_permisos',
            'gestionar_usuarios',
            'gestionar_asistencias',
            'ver_reportes',
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(['nombre' => $permiso]);
        }
    }
}
