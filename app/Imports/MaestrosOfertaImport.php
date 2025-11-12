<?php

namespace App\Imports;

use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Docente;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\Usuario;
use App\Models\GrupoMateria;
use App\Models\HorarioMateria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Throwable;

class MaestrosOfertaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $notificaciones = [];

        // Procesamos todo en una transacción global para seguridad
        DB::transaction(function () use ($rows, &$notificaciones) {
            foreach ($rows as $index => $row) {
                // Normalizar campos del Excel (encabezados: sigla, grupo, materia, docente, aula, dia1, hora_inicio1, hora_fin1, ...)
                $sigla = trim((string)($row['sigla'] ?? ''));
                $grupoNombre = trim((string)($row['grupo'] ?? ''));
                $materiaNombre = trim((string)($row['materia'] ?? ''));
                $docenteNombre = trim((string)($row['docente'] ?? ''));
                $aulaNro = trim((string)($row['aula'] ?? ''));
                $capacidad = trim((string)($row['capacidad'] ?? ''));
                $semestre = trim((string)($row['semestre'] ?? ''));
                // Si faltan campos obligatorios, saltar
                if (!$sigla || !$grupoNombre || !$materiaNombre || !$docenteNombre) {
                    $notificaciones[] = "Fila " . ($index + 2) . " omitida: faltan campos obligatorios (sigla/grupo/materia/docente).";
                    continue;
                }

                // 1) Materia (se reutiliza si existe)
                $materia = Materia::firstOrCreate(
                    ['sigla' => $sigla],
                    ['nombre' => $materiaNombre, 'semestre' => $semestre ?: 0]
                );

                // 2) Grupo
                $grupo = Grupo::firstOrCreate(['nombre' => $grupoNombre]);

                // 3) Usuario/docente: **NO crear nuevos docentes.**
                $usuarioDocente = Usuario::where('nombre', $docenteNombre)->first();
                if (!$usuarioDocente) {
                    $notificaciones[] = "Fila " . ($index + 2) . ": docente '{$docenteNombre}' no existe en usuarios — omitiendo filas relacionadas.";
                    continue;
                }

                $docente = Docente::where('registro', $usuarioDocente->registro)->first();
                if (!$docente) {
                    $notificaciones[] = "Fila " . ($index + 2) . ": docente '{$docenteNombre}' no tiene perfil docente (tabla 'docente') — omitiendo filas relacionadas.";
                    continue;
                }

                // 4) Aula (si viene)
                $aula = null;
                if ($aulaNro !== '') {
                    // Si tu campo 'nro' en aulas es integer, castearlo:
                    $aulaKey = is_numeric($aulaNro) ? (int)$aulaNro : $aulaNro;
                    $aula = Aula::firstOrCreate(
                        ['nro' => $aulaKey],
                        ['capacidad' => is_numeric($capacidad) ? (int)$capacidad : 0, 'piso' => is_numeric($aulaNro) ? floor((int)$aulaNro / 10) : 1]
                    );
                }

                // 5) GrupoMateria
                $grupoMateria = GrupoMateria::firstOrCreate([
                    'grupo_id' => $grupo->id,
                    'materia_sigla' => $materia->sigla,
                    'docente_registro' => $usuarioDocente->registro,
                ]);

                // 6) Iterar posibles días/horas (hasta dia4/hora_inicio4/hora_fin4)
                for ($i = 1; $i <= 4; $i++) {
                    $dia = strtolower(trim((string)($row["dia{$i}"] ?? '')));
                    $hIniRaw = $row["hora_inicio{$i}"] ?? '';
                    $hFinRaw = $row["hora_fin{$i}"] ?? '';

                    $horaInicio = $this->excelTimeToString($hIniRaw);
                    $horaFin = $this->excelTimeToString($hFinRaw);

                    if (empty($dia) || empty($horaInicio) || empty($horaFin)) {
                        continue; // nada que hacer en esta columna
                    }

                    // Validar formato de horas robustamente
                    try {
                        $inicioDt = Carbon::createFromFormat('H:i', $horaInicio);
                    } catch (Throwable $e) {
                        try { $inicioDt = Carbon::parse($horaInicio); } catch (Throwable $e2) { 
                            $notificaciones[] = "Fila " . ($index + 2) . ": hora de inicio inválida '{$hIniRaw}' -> omitido ({$dia}).";
                            continue;
                        }
                    }
                    try {
                        $finDt = Carbon::createFromFormat('H:i', $horaFin);
                    } catch (Throwable $e) {
                        try { $finDt = Carbon::parse($horaFin); } catch (Throwable $e2) {
                            $notificaciones[] = "Fila " . ($index + 2) . ": hora de fin inválida '{$hFinRaw}' -> omitido ({$dia}).";
                            continue;
                        }
                    }

                    // Asegurar que inicio < fin
                    if ($finDt->lessThanOrEqualTo($inicioDt)) {
                        $notificaciones[] = "Fila " . ($index + 2) . ": hora fin debe ser mayor que hora inicio ({$horaInicio} - {$horaFin}) -> omitido.";
                        continue;
                    }

                    $duracionHoras = $finDt->diffInMinutes($inicioDt) / 60.0;

                    // --- 6.a) Calcular carga actual del docente (en horas)
                    $cargaActual = HorarioMateria::whereHas('grupoMateria', function ($q) use ($usuarioDocente) {
                        $q->where('docente_registro', $usuarioDocente->registro);
                    })->get()->sum(function ($hm) {
                        // $hm tiene relación horario cargada? si no, la cargamos
                        $horaInicioExist = $hm->horario->hora_inicio ?? null;
                        $horaFinExist = $hm->horario->hora_fin ?? null;
                        try {
                            $ini = Carbon::createFromFormat('H:i', $horaInicioExist);
                        } catch (Throwable $e) {
                            try { $ini = Carbon::parse($horaInicioExist); } catch (Throwable $e2) { return 0; }
                        }
                        try {
                            $fin = Carbon::createFromFormat('H:i', $horaFinExist);
                        } catch (Throwable $e) {
                            try { $fin = Carbon::parse($horaFinExist); } catch (Throwable $e2) { return 0; }
                        }
                        return $fin->diffInMinutes($ini) / 60.0;
                    });

                    $cargaMax = $docente->carga_horaria_max ?? 40;

                    if (($cargaActual + $duracionHoras) > $cargaMax) {
                        $notificaciones[] = "⚠️ {$usuarioDocente->nombre} no se asignó {$materia->nombre} ({$dia} {$horaInicio}-{$horaFin}) → excede carga horaria (actual: {$this->roundf($cargaActual)}h, bloque: {$this->roundf($duracionHoras)}h, máximo: {$cargaMax}h).";
                        continue;
                    }

                    // --- 6.b) Verificar solapamiento de docente: NO puede tener dos clases al mismo tiempo, aunque en distintas aulas
                    // Buscamos horarios existentes del docente el mismo día que solapen por intervalo.
                    $docenteChoque = HorarioMateria::whereHas('grupoMateria', function ($q) use ($usuarioDocente) {
                        $q->where('docente_registro', $usuarioDocente->registro);
                    })->whereHas('horario', function ($q) use ($dia, $horaInicio, $horaFin) {
                        $q->where('dia', $dia)
                          ->where(function ($q2) use ($horaInicio, $horaFin) {
                              // overlap: inicio_exist < new_fin AND new_inicio < fin_exist
                              $q2->whereRaw("hora_inicio < ? AND ? < hora_fin", [$horaFin, $horaInicio]);
                          });
                    })->exists();

                    if ($docenteChoque) {
                        $notificaciones[] = "⛔ {$usuarioDocente->nombre} tiene choque de horario con otra materia el {$dia} {$horaInicio}-{$horaFin}.";
                        continue;
                    }

                    // --- 6.c) Verificar solapamiento de aula: SIEMPRE evitar si es la misma aula.
                    if ($aula) {
                        $aulaChoque = HorarioMateria::where('nro', $aula->nro)
                            ->whereHas('horario', function ($q) use ($dia, $horaInicio, $horaFin) {
                                $q->where('dia', $dia)
                                  ->where(function ($q2) use ($horaInicio, $horaFin) {
                                      $q2->whereRaw("hora_inicio < ? AND ? < hora_fin", [$horaFin, $horaInicio]);
                                  });
                            })->exists();

                        if ($aulaChoque) {
                            $notificaciones[] = "🚫 Aula {$aula->nro} ocupada el {$dia} {$horaInicio}-{$horaFin} → no se asignó {$materia->nombre}.";
                            continue;
                        }
                    }
                    // Nota: se permiten solapamientos si son en aulas distintas (ya permitido por lógica).

                    // --- 6.d) Crear o recuperar horario
                    $horario = Horario::firstOrCreate(
                        [
                            'dia' => $dia,
                            'hora_inicio' => $horaInicio,
                            'hora_fin' => $horaFin
                        ]
                    );

                    // --- 6.e) Relacionar horario con grupo_materia (y aula nro)
                    HorarioMateria::firstOrCreate([
                        'grupo_materia_id' => $grupoMateria->id,
                        'horario_id' => $horario->id,
                        'nro' => $aula ? $aula->nro : null,
                    ]);
                } // end for dias
            } // end foreach rows
        }); // end transaction

        // Guardar notificaciones en sesión y log
        if (!empty($notificaciones)) {
            Session::flash('notificaciones', $notificaciones);
            foreach ($notificaciones as $m) {
                Log::warning($m);
            }
        }
    }

    /**
     * Convierte valores numéricos de Excel a formato HH:MM
     * Acepta: '07:00', '7:00', '7:00:00', o valor decimal de Excel (0.2916...)
     */
    private function excelTimeToString($value)
    {
        if (is_null($value) || $value === '') return null;

        $v = trim((string)$value);

        // formato HH:MM o H:MM o HH:MM:SS
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $v)) {
            // si tiene segundos, quitarlos
            if (strpos($v, ':') !== false) {
                $parts = explode(':', $v);
                return sprintf('%02d:%02d', (int)$parts[0], (int)$parts[1]);
            }
            return $v;
        }

        // si es numérico (decimal Excel) -> convertir a horas
        if (is_numeric($v)) {
            // php spreadsheet style: 1.0 = 24:00 -> convertir a segundos
            $totalSeconds = round((float)$v * 24 * 60 * 60);
            $h = floor($totalSeconds / 3600);
            $m = floor(($totalSeconds % 3600) / 60);
            return sprintf('%02d:%02d', $h, $m);
        }

        // intentar parse con Carbon (ej. '07:00 AM')
        try {
            $dt = Carbon::parse($v);
            return $dt->format('H:i');
        } catch (Throwable $e) {
            return null;
        }
    }

    private function roundf($num)
    {
        return round($num, 2);
    }
}