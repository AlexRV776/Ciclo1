<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioImportController extends Controller
{
    public function showImportForm()
    {
        if (!Auth::user()->tienePermiso('importar_usuarios_excel')) {
            abort(403, 'No tienes permiso para importar usuarios.');
        }

        return view('usuario.importar');
    }

    public function import(Request $request)
    {
        if (!Auth::user()->tienePermiso('importar_usuarios_excel')) {
            abort(403, 'No tienes permiso para importar usuarios.');
        }

        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        // Registrar bitácora
        registrarBitacora(Auth::user(), 'Importar Usuarios', $request,
            'Intentó importar usuarios desde un archivo Excel.'
        );

        return back()->with('success', 'Importación registrada en bitácora (acción no ejecutada).');
    }
}
