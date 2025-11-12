<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::with('permisos')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permiso::all();
        return view('admin.roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:roles,nombre|max:100',
            'descripcion' => 'nullable|max:255',
            'permisos' => 'array'
        ]);

        $rol = Rol::create($request->only('nombre', 'descripcion'));
        $rol->permisos()->sync($request->input('permisos', []));

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Crear Rol', $request,
            "Se creó el rol '{$rol->nombre}' con permisos asignados."
        );

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Rol $rol)
    {
        $permisos = Permiso::all();
        $rol->load('permisos');
        return view('admin.roles.edit', compact('rol', 'permisos'));
    }

    public function update(Request $request, Rol $rol)
    {
        $request->validate([
            'nombre' => 'required|max:100|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'nullable|max:255',
            'permisos' => 'array'
        ]);

        $anterior = $rol->nombre;
        $rol->update($request->only('nombre', 'descripcion'));
        $rol->permisos()->sync($request->input('permisos', []));

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar Rol', $request,
            "Se actualizó el rol de nombre '{$anterior}'."
        );

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $rol)
    {
        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Eliminar Rol', request(),
            "Se eliminó el rol '{$rol->nombre}'."
        );

        $rol->permisos()->detach();
        $rol->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
