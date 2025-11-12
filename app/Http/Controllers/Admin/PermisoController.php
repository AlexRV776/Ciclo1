<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::all();
        return view('admin.permisos.index', compact('permisos'));
    }

    public function create()
    {
        return view('admin.permisos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:permisos,nombre|max:100',
        ]);

        $permiso = Permiso::create($request->only('nombre'));

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Crear Permiso', $request,
            "Se creó el permiso con nombre '{$permiso->nombre}'."
        );

        return redirect()->route('admin.permisos.index')->with('success', 'Permiso creado correctamente.');
    }

    public function edit(Permiso $permiso)
    {
        return view('admin.permisos.edit', compact('permiso'));
    }

    public function update(Request $request, Permiso $permiso)
    {
        $request->validate([
            'nombre' => 'required|max:100|unique:permisos,nombre,' . $permiso->id,
        ]);

        $anterior = $permiso->nombre;
        $permiso->update($request->only('nombre'));

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar Permiso', $request,
            "Se actualizó el permiso de nombre '{$anterior}' a '{$permiso->nombre}'."
        );

        return redirect()->route('admin.permisos.index')->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(Permiso $permiso)
    {
        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Eliminar Permiso', request(),
            "Se eliminó el permiso con nombre '{$permiso->nombre}'."
        );

        $permiso->delete();

        return redirect()->route('admin.permisos.index')->with('success', 'Permiso eliminado correctamente.');
    }
}
