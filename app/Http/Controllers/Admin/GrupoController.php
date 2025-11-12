<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    // Mostrar todos los grupos
    public function index()
    {
        $grupos = Grupo::all();
        return view('admin.grupos.index', compact('grupos'));
    }

    // Mostrar formulario para crear grupo
    public function create()
    {
        return view('admin.grupos.create');
    }

    // Guardar grupo nuevo
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:grupo,nombre',
        ]);

        $grupo = Grupo::create([
            'nombre' => $request->nombre,
        ]);
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Crear Grupo', $request, "Se creó el grupo '{$grupo->nombre}'.");

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo creado correctamente.');
    }

    // Mostrar formulario para editar grupo
    public function edit(Grupo $grupo)
    {
        return view('admin.grupos.edit', compact('grupo'));
    }

    // Actualizar grupo
    public function update(Request $request, Grupo $grupo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:grupo,nombre,' . $grupo->id,
        ]);

        $nombreAnterior = $grupo->nombre;
        $grupo->update(['nombre' => $request->nombre]);
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar Grupo', $request, "Se actualizó el grupo '{$nombreAnterior}' a '{$grupo->nombre}'.");

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo actualizado correctamente.');
    }

    // Eliminar grupo
    public function destroy(Grupo $grupo)
    {
        $nombre = $grupo->nombre;
        $grupo->delete();
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Eliminar Grupo', request(), "Se eliminó el grupo '{$nombre}'.");

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo eliminado correctamente.');
    }
}
