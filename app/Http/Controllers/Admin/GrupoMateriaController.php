<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupoMateria;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Docente;
use Illuminate\Http\Request;

class GrupoMateriaController extends Controller
{
    public function index()
    {
        $relaciones = GrupoMateria::with(['grupo', 'materia', 'docente'])->get();
        return view('admin.grupo_materia.index', compact('relaciones'));
    }

    public function create()
    {
        $grupos = Grupo::all();
        $materias = Materia::all();
        $docentes = Docente::with('usuario')->get();

        return view('admin.grupo_materia.create', compact('grupos', 'materias', 'docentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupo,id',
            'materia_sigla' => 'required|exists:materia,sigla',
            'docente_registro' => 'required|exists:docente,registro',
        ]);

        GrupoMateria::create($request->all());

        return redirect()->route('admin.grupo_materia.index')
                        ->with('success', 'Asignación creada correctamente');
    }

    public function edit($id)
    {
        $relacion = GrupoMateria::findOrFail($id);
        $grupos = Grupo::all();
        $materias = Materia::all();
        $docentes = Docente::with('usuario')->get();

        return view('admin.grupo_materia.edit', compact('relacion', 'grupos', 'materias', 'docentes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupo,id',
            'materia_sigla' => 'required|exists:materia,sigla',
            'docente_registro' => 'required|exists:docente,registro',
        ]);

        $relacion = GrupoMateria::findOrFail($id);
        $relacion->update($request->all());

        return redirect()->route('admin.grupo_materia.index')
                        ->with('success', 'Asignación actualizada correctamente');
    }

    public function destroy($id)
    {
        $relacion = GrupoMateria::findOrFail($id);
        $relacion->delete();

        return redirect()->route('admin.grupo_materia.index')
                        ->with('success', 'Asignación eliminada correctamente');
    }
}