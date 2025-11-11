<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;// si usas barryvdh/laravel-dompdf
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    public function personal(Request $request)
    {
        // Enviar roles para el select
        $roles = \App\Models\Rol::all();
        registrarBitacora(Auth::user(), 'reportes_personal', $request, 'ingreso a ver el personal');
        return view('admin.reportes.personal', compact('roles'));

    }

    public function exportPersonal(Request $request)
    {
        $tipo = $request->input('tipo');
        // Construir consulta con filtros dinámicos
        $query = Usuario::select('registro','nombre','correo','rol_id')
            ->with('rol');
        // Filtro por rol
        if ($request->rol_id) {
            $query->where('rol_id', $request->rol_id);
        }

        // Filtro por fecha de contratación
        if ($request->fecha_desde) {
            $query->whereHas('docente', function ($q) use ($request) {
                $q->where('fecha_contrato', '>=', $request->fecha_desde);
            });
        }

        if ($request->fecha_hasta) {
            $query->whereHas('docente', function ($q) use ($request) {
                $q->where('fecha_contrato', '<=', $request->fecha_hasta);
            });
        }

        $personal = $query->get();

        // Exportar PDF
        if ($tipo === 'pdf') {
            registrarBitacora(Auth::user(), 'reporte_Personal', $request, 'Exporto datos de personal en pdf');
            $pdf = PDF::loadView('admin.reportes.personal_pdf', compact('personal'));
            return $pdf->download('reporte_personal.pdf');
        }

        // Exportar Excel
        if ($tipo === 'excel') {
            registrarBitacora(Auth::user(), 'reporte_Personal', $request, 'Exporto datos de personal en excel');
            return Excel::download(
                new \App\Exports\PersonalExport($personal),
                'reporte_personal.xlsx'
            );
        }

        return back()->with('error', 'Tipo de reporte no válido');
    }
}