<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HorarioMateria;
use App\Models\Asistencia;
use Carbon\Carbon;

class MarcarFaltasCommand extends Command
{
    protected $signature = 'asistencia:marcar-faltas';
    protected $description = 'Marca automáticamente las faltas de docentes que no registraron asistencia a tiempo';

    public function handle()
    {
        $ahora = Carbon::now('America/La_Paz');

        // Buscar clases del día que ya pasaron su límite (inicio + 30 min)
        $horarios = HorarioMateria::with('horario', 'grupoMateria')
            ->whereHas('horario', function ($q) use ($ahora) {
                $q->where('dia', strtolower($ahora->dayName));
            })
            ->get();

        foreach ($horarios as $hm) {

            $horaInicio = Carbon::parse($hm->horario->hora_inicio, 'America/La_Paz');
            $limite = $horaInicio->copy()->addMinutes(30);

            // Si ya pasó el límite
            if ($ahora->greaterThan($limite)) {

                $registro = $hm->grupoMateria->docente_registro;

                // Ya marcó?
                $existe = Asistencia::where('docente_registro', $registro)
                    ->where('horario_materia_id', $hm->id)
                    ->whereDate('fecha', $ahora->toDateString())
                    ->exists();

                if (!$existe) {
                    // Registrar falta
                    Asistencia::create([
                        'fecha' => $ahora->toDateString(),
                        'modalidad' => 'presencial', // no importa, puedes poner null si quieres
                        'estado' => 'falta',
                        'docente_registro' => $registro,
                        'horario_materia_id' => $hm->id,
                    ]);
                }
            }
        }

        return Command::SUCCESS;
    }
}
