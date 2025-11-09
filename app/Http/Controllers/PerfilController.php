<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function edit()
    {
        $usuario = Auth::user();
        return view('usuario.perfil', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|max:150|unique:usuarios,correo,' . $usuario->registro . ',registro',
            'password' => 'nullable|min:6'
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->correo = $request->correo;

        if ($request->password) {
            $usuario->contrasena = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }
}