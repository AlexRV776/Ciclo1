<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HorarioMateria;
use App\Models\GrupoMateria;
use App\Models\Horario;
use App\Models\Aula;
use Illuminate\Http\Request;

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
        if ($this->docenteTieneConflicto($request->grupo_materia_id, $request->horario_id)) {
            return back()->with('error', '⚠️ El docente ya tiene una materia en ese horario.');
        }
        $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materia,id',
            'horario_id' => 'required|exists:horario,id',
            'nro' => 'required|exists:aulas,nro',
        ]);

        HorarioMateria::create($request->all());

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
        if ($this->docenteTieneConflicto($request->grupo_materia_id, $request->horario_id, $horario_materium->id)) {
            return back()->with('error', '⚠️ El docente ya tiene una materia en ese horario.');
        }
        $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materia,id',
            'horario_id' => 'required|exists:horario,id',
            'nro' => 'required|exists:aulas,nro',
        ]);

        $horario_materium->update($request->all());

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario actualizado.');
    }

    public function destroy(HorarioMateria $horario_materium)
    {
        $horario_materium->delete();

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario eliminado.');
    }

    private function docenteTieneConflicto($grupo_materia_id, $horario_id, $ignorarId = null)
    {
        $gm = \App\Models\GrupoMateria::find($grupo_materia_id);

        if (!$gm) return false;

        $docente = $gm->docente_registro;

        $horario = \App\Models\Horario::find($horario_id);

        if (!$horario) return false;

        $query = \App\Models\HorarioMateria::whereHas('grupoMateria', function ($q) use ($docente) {
            $q->where('docente_registro', $docente);
        })
        ->whereHas('horario', function ($q) use ($horario) {
            $q->where('dia', $horario->dia)
                ->where('hora_inicio', $horario->hora_inicio)
                ->where('hora_fin', $horario->hora_fin);
        });

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }
}