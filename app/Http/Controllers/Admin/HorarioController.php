<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::all();
        return view('admin.horario.index', compact('horarios'));
    }

    public function create()
    {
        return view('admin.horario.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'dia' => 'required|string|max:20',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $horario = Horario::create($request->all());
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Crear Horario', $request,
            "Se creó un horario para {$horario->dia} de {$horario->hora_inicio} a {$horario->hora_fin}."
        );

        return redirect()->route('admin.horario.index')->with('success', 'Horario registrado correctamente.');
    }

    public function edit(Horario $horario)
    {
        return view('admin.horario.edit', compact('horario'));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
            'dia' => 'required|string|max:20',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $anterior = $horario->toArray();
        $horario->update($request->all());
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Actualizar Horario', $request,
            "Se actualizó el horario de {$anterior['dia']} ({$anterior['hora_inicio']} - {$anterior['hora_fin']}) a {$horario->dia} ({$horario->hora_inicio} - {$horario->hora_fin})."
        );

        return redirect()->route('admin.horario.index')->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Horario $horario)
    {
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Eliminar Horario', request(),
            "Se eliminó el horario de {$horario->dia} de {$horario->hora_inicio} a {$horario->hora_fin}."
        );

        $horario->delete();

        return redirect()->route('admin.horario.index')->with('success', 'Horario eliminado correctamente.');
    }
}
