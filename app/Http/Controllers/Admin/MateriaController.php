<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::all();
        return view('admin.materias.index', compact('materias'));
    }

    public function create()
    {
        return view('admin.materias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sigla' => 'required|string|max:10|unique:materia,sigla',
            'nombre' => 'required|string|max:100',
            'semestre' => 'required|integer|min:1|max:12',
        ]);

        $materia = Materia::create($request->all());

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Crear Materia', $request,
            "Se creó la materia con sigla {$materia->sigla}, nombre {$materia->nombre}, semestre {$materia->semestre}."
        );

        return redirect()->route('admin.materias.index')->with('success', 'Materia creada correctamente');
    }

    public function edit(Materia $materia)
    {
        return view('admin.materias.edit', compact('materia'));
    }

    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'semestre' => 'required|integer|min:1|max:12',
        ]);

        $anterior = $materia->toArray();
        $materia->update($request->only('nombre', 'semestre'));

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar Materia', $request,
            "Se actualizó la materia sigla {$materia->sigla}: nombre '{$anterior['nombre']}' -> '{$materia->nombre}', semestre {$anterior['semestre']} -> {$materia->semestre}."
        );

        return redirect()->route('admin.materias.index')->with('success', 'Materia actualizada correctamente');
    }

    public function destroy(Materia $materia)
    {
        // Registrar bitácora
        registrarBitacora( Auth::user(), 'Eliminar Materia', request(),
            "Se eliminó la materia sigla {$materia->sigla}, nombre {$materia->nombre}, semestre {$materia->semestre}."
        );

        $materia->delete();

        return redirect()->route('admin.materias.index')->with('success', 'Materia eliminada correctamente');
    }
}
