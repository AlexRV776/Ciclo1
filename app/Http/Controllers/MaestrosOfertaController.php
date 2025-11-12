<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\MaestrosOfertaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class MaestrosOfertaController extends Controller
{
    public function index()
    {
        // Vista para subir el Excel
        return view('admin.maestros_oferta.importar');
    }

    public function importar(Request $request)
    {
        // Verifica permiso
        if (!Auth::user()->tienePermiso('importar_oferta')) {
            abort(403, 'No tienes permiso para importar oferta.');
        }

        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new MaestrosOfertaImport, $request->file('archivo'));
        //Registrar bitácora
        registrarBitacora(Auth::user(), 'Importar oferta', $request, 'Se importó un archivo de oferta de maestros');
        return back()->with('success', 'Archivo importado correctamente 🎉');
    }
}