<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Docente;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::with('usuario')->get();
        return view('admin.docentes.index', compact('docentes'));
    }

    // Mostrar formulario para editar docente
    public function edit(Docente $docente)
    {
        return view('admin.docentes.edit', compact('docente'));
    }

    public function update(Request $request, Docente $docente)
    {
        $request->validate([
            'especialidad' => 'required|string|max:100',
            'sueldo' => 'required|numeric|min:0',
        ]);

        $docente->update([
            'especialidad' => $request->especialidad,
            'sueldo' => $request->sueldo,
        ]);

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar Docente', $request, "Se actualizó el docente con registro {$docente->registro} ({$docente->usuario->nombre}).");

        return redirect()->route('admin.docentes.index')
                         ->with('success', 'Datos del docente actualizados correctamente.');
    }

    // Eliminar docente
    public function destroy(Docente $docente)
    {
        $nombreDocente = $docente->usuario->nombre ?? 'Desconocido';
        $registro = $docente->registro;

        $docente->delete();

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Eliminar Docente', request(), "Se eliminó el docente con registro {$registro} ({$nombreDocente}).");

        return redirect()->route('admin.docentes.index')->with('success', 'Docente eliminado correctamente.');
    }
}
