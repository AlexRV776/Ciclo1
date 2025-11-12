<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioMateria;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        // ✅ Verificar permiso
        if (!$usuario->tienePermiso('ver_calendario')) {
            abort(403, 'No tienes permiso para ver el calendario.');
        }

        // ✅ Si el usuario tiene perfil docente
        if (!$usuario->docente) {
            abort(403, 'Solo los docentes pueden ver el calendario.');
        }

        $registro = $usuario->registro;

        // 🔍 Buscar todos los horarios asociados al docente
        $horarios = HorarioMateria::with(['horario', 'aula', 'grupoMateria.materia', 'grupoMateria.grupo'])
            ->whereHas('grupoMateria', function ($q) use ($registro) {
                $q->where('docente_registro', $registro);
            })
            ->get();

        // 🔁 Organizar por día de la semana
        $porDia = $horarios->groupBy(fn($hm) => $hm->horario->dia);

        return view('docente.calendario', compact('porDia'));
    }
}