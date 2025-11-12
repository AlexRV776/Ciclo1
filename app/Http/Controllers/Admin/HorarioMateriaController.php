<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HorarioMateria;
use App\Models\GrupoMateria;
use App\Models\Horario;
use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorarioMateriaController extends Controller
{
    public function index()
    {
        $asignaciones = HorarioMateria::with(['grupoMateria.grupo', 'grupoMateria.materia', 'horario', 'aula'])->get();
        return view('admin.horario_materia.index', compact('asignaciones'));
    }

    public function create()
    {
        $grupoMaterias = GrupoMateria::with(['grupo', 'materia'])->get();
        $horarios = Horario::all();
        $aulas = Aula::all();

        return view('admin.horario_materia.create', compact('grupoMaterias', 'horarios', 'aulas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materia,id',
            'horario_id' => 'required|exists:horario,id',
            'nro' => 'required|exists:aulas,nro',
        ]);

        if ($this->docenteTieneConflicto($request->grupo_materia_id, $request->horario_id)) {
            return back()->with('error', '⚠️ El docente ya tiene una materia en ese día y hora.');
        }

        $horarioMateria = HorarioMateria::create($request->all());

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Crear HorarioMateria', $request,
            "Se asignó horario al grupo_materia_id {$horarioMateria->grupo_materia_id}, horario_id {$horarioMateria->horario_id}, aula {$horarioMateria->nro}."
        );

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario asignado correctamente.');
    }

    public function edit(HorarioMateria $horario_materium)
    {
        $grupoMaterias = GrupoMateria::with(['grupo', 'materia'])->get();
        $horarios = Horario::all();
        $aulas = Aula::all();

        return view('admin.horario_materia.edit', compact('horario_materium', 'grupoMaterias', 'horarios', 'aulas'));
    }

    public function update(Request $request, HorarioMateria $horario_materium)
    {
        $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materia,id',
            'horario_id' => 'required|exists:horario,id',
            'nro' => 'required|exists:aulas,nro',
        ]);

        if ($this->docenteTieneConflicto($request->grupo_materia_id, $request->horario_id, $horario_materium->id)) {
            return back()->with('error', '⚠️ El docente ya tiene una materia en ese día y hora.');
        }

        $anterior = $horario_materium->toArray();
        $horario_materium->update($request->all());

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar HorarioMateria', $request,
            "Se actualizó horarioMateria ID {$horario_materium->id} de grupo_materia_id {$anterior['grupo_materia_id']}, horario_id {$anterior['horario_id']}, aula {$anterior['nro']} a grupo_materia_id {$horario_materium->grupo_materia_id}, horario_id {$horario_materium->horario_id}, aula {$horario_materium->nro}."
        );

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario actualizado.');
    }

    public function destroy(HorarioMateria $horario_materium)
    {
        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Eliminar HorarioMateria', request(),
            "Se eliminó horarioMateria ID {$horario_materium->id}, grupo_materia_id {$horario_materium->grupo_materia_id}, horario_id {$horario_materium->horario_id}, aula {$horario_materium->nro}."
        );

        $horario_materium->delete();

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario eliminado.');
    }

    /**
     * Verifica si el docente ya tiene un horario asignado en el mismo día y hora
     */
    private function docenteTieneConflicto($grupo_materia_id, $horario_id, $ignorarId = null)
    {
        $gm = GrupoMateria::find($grupo_materia_id);
        if (!$gm) return false;

        $docente = $gm->docente_registro;
        $horario = Horario::find($horario_id);
        if (!$horario) return false;

        $query = HorarioMateria::whereHas('grupoMateria', function ($q) use ($docente) {
                $q->where('docente_registro', $docente);
            })
            ->whereHas('horario', function ($q) use ($horario) {
                $q->where('dia', $horario->dia)
                  ->where(function($q2) use ($horario) {
                      $q2->whereBetween('hora_inicio', [$horario->hora_inicio, $horario->hora_fin])
                         ->orWhereBetween('hora_fin', [$horario->hora_inicio, $horario->hora_fin])
                         ->orWhere(function($q3) use ($horario) {
                             $q3->where('hora_inicio', '<=', $horario->hora_inicio)
                                ->where('hora_fin', '>=', $horario->hora_fin);
                         });
                  });
            });

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }
}
