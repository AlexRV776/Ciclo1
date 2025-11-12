<?php

namespace App\Imports;

use App\Models\Usuario;
use App\Models\Docente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class UsuariosImport implements ToModel, WithHeadingRow
{
    /**
     * Normaliza y convierte a Carbon una posible fecha proveniente del excel.
     * Acepta: número serial de Excel (45972), '11/11/2025', '2025-11-11', etc.
     */
    protected function parseFecha($valor)
    {
        if (is_null($valor) || $valor === '') {
            return null;
        }

        // Si es numérico: probable serial de Excel
        if (is_numeric($valor)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject((float) $valor);
                return Carbon::instance($dt)->toDateString(); // 'YYYY-MM-DD'
            } catch (\Exception $e) {
                // si falla, seguir a intentar parsear como texto
            }
        }

        // Intentar parseo con Carbon (soporta varios formatos)
        try {
            // Reemplaza barras por guiones para ayudar al parseo si es '11/11/2025'
            $norm = str_replace('/', '-', trim($valor));
            $carb = Carbon::parse($norm);
            return $carb->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function model(array $row)
    {
        // Normalizar claves (headingRowFormatter por defecto convierte a snake_case,
        // pero por seguridad usamos índices esperados)
        $registro       = $row['registro'] ?? null;
        $nombre         = $row['nombre'] ?? null;
        $correo         = $row['correo'] ?? null;
        // OJO: en tu excel usa 'contrasena' SIN ñ
        $contrasena     = $row['contrasena'] ?? ($row['contraseña'] ?? null);
        $rol_id         = isset($row['rol_id']) ? (int)$row['rol_id'] : null;
        $estado_raw     = $row['estado'] ?? null;
        $fecha_raw      = $row['fecha_contrato'] ?? null;
        $especialidad   = $row['especialidad'] ?? null;
        $sueldo_raw     = $row['sueldo'] ?? 0;

        // Procesar estado
        $estado = true; // valor por defecto
        if (!is_null($estado_raw)) {
            $lower = strtolower(trim((string)$estado_raw));
            if (in_array($lower, ['0', 'false', 'inactivo', 'no', 'n'])) {
                $estado = false;
            } elseif (in_array($lower, ['1', 'true', 'activo', 'si', 'sí', 's'])) {
                $estado = true;
            }
        }

        // Procesar fecha (soporta serial de excel o texto)
        $fecha_contrato = $this->parseFecha($fecha_raw);

        // Asegurar sueldo numérico
        $sueldo = is_numeric($sueldo_raw) ? (float)$sueldo_raw : 0;

        // Si no hay registro, saltar (o podrías lanzar error)
        if (empty($registro)) {
            return null;
        }

        // Crear o actualizar Usuario
        $usuario = Usuario::updateOrCreate(
            ['registro' => $registro],
            [
                'nombre' => $nombre,
                'correo' => $correo,
                // Si en tu tabla la columna es 'contrasena' (ya en tu modelo),
                // guardamos hashed. Si quieres guardar sin hash, elimina Hash::make.
                'contrasena' => $contrasena ? Hash::make((string)$contrasena) : Hash::make(Str::random(8)),
                'rol_id' => $rol_id,
                'estado' => $estado ?? 'activo',
            ]
        );

        // Si el rol existe y su nombre es 'docente' O si rol_id indica docente, crear Docente
        // (ajusta la lógica según tu tabla roles)
        $esDocente = false;
        try {
            if ($usuario->rol && strtolower($usuario->rol->nombre) === 'docente') {
                $esDocente = true;
            }
        } catch (\Throwable $e) {
            // ignorar si rol no está cargada
        }
        // Si tu sistema usa rol_id 2 = docente (ejemplo), puedes chequear:
        if (!$esDocente && $rol_id === 2) {
            $esDocente = true;
        }

        if ($esDocente) {
            Docente::updateOrCreate(
                ['registro' => $usuario->registro],
                [
                    // Fecha en formato 'YYYY-MM-DD' o null
                    'fecha_contrato' => $fecha_contrato,
                    'especialidad' => $especialidad,
                    'sueldo' => $sueldo,
                ]
            );
        }

        return $usuario;
    }
}