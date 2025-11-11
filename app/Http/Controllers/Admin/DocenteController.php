<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Docente;

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

    return redirect()->route('admin.docentes.index')
                     ->with('success', 'Datos del docente actualizados correctamente.');
}


    // Eliminar grupo
    public function destroy(Docente $docente)
    {
        $docente->delete();
        return redirect()->route('admin.docentes.index')->with('success', 'Docente eliminado correctamente.');
    }
}