<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Docente;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsuariosImport;
use Illuminate\Support\Facades\Auth;

class UsuarioImportController extends Controller
{
    public function showImportForm()
    {
        // Solo si tiene permiso
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
        registrarBitacora(Auth::user(), 'Importar Usuarios', $request,
            'Intentó importar usuarios desde un archivo Excel.'
        );
        Excel::import(new UsuariosImport, $request->file('archivo'));

        return back()->with('success', 'Usuarios importados correctamente.');
    }
}
