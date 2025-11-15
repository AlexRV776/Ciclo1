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

        $gm = GrupoMateria::with('docente')->find($request->grupo_materia_id);
        $horario = Horario::find($request->horario_id);

        // 1️⃣ Validar conflicto del docente
        if ($this->docenteTieneConflicto($gm->docente_registro, $horario, null)) {
            return back()->with('error', '⚠️ El docente ya tiene una materia asignada en este horario.');
        }

        // 2️⃣ Validar conflicto de aula (solo si es la misma aula)
        if ($this->aulaOcupada($request->nro, $horario, null)) {
            return back()->with('error', '⚠️ El aula ya está ocupada en este horario.');
        }

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
        $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materia,id',
            'horario_id' => 'required|exists:horario,id',
            'nro' => 'required|exists:aulas,nro',
        ]);

        $gm = GrupoMateria::with('docente')->find($request->grupo_materia_id);
        $horario = Horario::find($request->horario_id);

        // 1️⃣ Validar conflicto docente
        if ($this->docenteTieneConflicto($gm->docente_registro, $horario, $horario_materium->id)) {
            return back()->with('error', '⚠️ El docente ya tiene una materia asignada en este horario.');
        }

        // 2️⃣ Validar conflicto de aula
        if ($this->aulaOcupada($request->nro, $horario, $horario_materium->id)) {
            return back()->with('error', '⚠️ El aula ya está ocupada en este horario.');
        }

        $horario_materium->update($request->all());

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(HorarioMateria $horario_materium)
    {
        $horario_materium->delete();

        return redirect()->route('admin.horario_materia.index')
            ->with('success', 'Horario eliminado.');
    }


    /* ─────────────────────────────────────────────── */
    /* VALIDACIONES                                    */
    /* ─────────────────────────────────────────────── */

    private function docenteTieneConflicto($docenteRegistro, $horario, $ignorarId = null)
    {
        return HorarioMateria::whereHas('grupoMateria', function($q) use ($docenteRegistro) {
                    $q->where('docente_registro', $docenteRegistro);
                })
                ->whereHas('horario', function($q) use ($horario) {
                    $q->where('dia', $horario->dia)
                      ->where(function($q2) use ($horario) {
                        $q2->whereBetween('hora_inicio', [$horario->hora_inicio, $horario->hora_fin])
                           ->orWhereBetween('hora_fin', [$horario->hora_inicio, $horario->hora_fin])
                           ->orWhere(function($q3) use ($horario) {
                               $q3->where('hora_inicio', '<=', $horario->hora_inicio)
                                  ->where('hora_fin', '>=', $horario->hora_fin);
                           });
                      });
                })
                ->when($ignorarId, fn($q) => $q->where('id', '!=', $ignorarId))
                ->exists();
    }

    private function aulaOcupada($aulaNro, $horario, $ignorarId = null)
    {
        return HorarioMateria::where('nro', $aulaNro)
                ->whereHas('horario', function($q) use ($horario) {
                    $q->where('dia', $horario->dia)
                      ->where(function($q2) use ($horario) {
                        $q2->whereBetween('hora_inicio', [$horario->hora_inicio, $horario->hora_fin])
                           ->orWhereBetween('hora_fin', [$horario->hora_inicio, $horario->hora_fin])
                           ->orWhere(function($q3) use ($horario) {
                               $q3->where('hora_inicio', '<=', $horario->hora_inicio)
                                  ->where('hora_fin', '>=', $horario->hora_fin);
                           });
                      });
                })
                ->when($ignorarId, fn($q) => $q->where('id', '!=', $ignorarId))
                ->exists();
    }

}