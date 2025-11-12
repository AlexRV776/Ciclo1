<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'Docente', 'descripcion' => 'Docente con acceso a asistencia'],
            ['nombre' => 'Secretaria', 'descripcion' => 'Secretaría académica'],
        ];

        foreach ($roles as $rolData) {
            $rol = Rol::updateOrCreate(
                ['nombre' => $rolData['nombre']],  // condición
                ['descripcion' => $rolData['descripcion']] // valores
            );

        }
    }
}
