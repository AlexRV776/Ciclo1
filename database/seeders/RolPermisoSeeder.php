<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) ADMINISTRADOR -> todos los permisos
        $admin = Rol::where('nombre', 'Administrador')->first();
        if ($admin) {
            $todos = Permiso::pluck('id')->toArray();
            // sync asegura que quede exactamente ese set (idempotente)
            $admin->permisos()->sync($todos);
        }

    }
}