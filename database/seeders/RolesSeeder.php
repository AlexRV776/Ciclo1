<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
class RolesSeeder extends Seeder
{

    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'Docente', 'descripcion' => 'Docente con acceso a asistencia'],
            ['nombre' => 'Secretaria', 'descripcion' => 'Secretaría académica'],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['nombre' => $rol['nombre']],  // condición
                ['descripcion' => $rol['descripcion']] // valores
            );
        }
    }
}