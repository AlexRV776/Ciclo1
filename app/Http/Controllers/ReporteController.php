<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Asistencia;
use App\Models\Docente;
use Barryvdh\DomPDF\Facade\Pdf;// si usas barryvdh/laravel-dompdf
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Imports\MaestrosOfertaImport;

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

    public function asistencia(Request $request)
    {
        $docentes = Docente::with('usuario')->get();
        registrarBitacora(Auth::user(), 'reportes_asistencia', $request, 'ingresó a ver reporte de asistencia');

        return view('admin.reportes.asistencia', compact('docentes'));
    }

    public function exportAsistencia(Request $request)
    {
        $tipo = $request->input('tipo');

        $query = Asistencia::with(['docente.usuario', 'horarioMateria.grupoMateria.materia'])
            ->orderBy('fecha', 'desc');

        // Filtro por docente
        if ($request->docente_registro) {
            $query->where('docente_registro', $request->docente_registro);
        }

        // Filtro por fecha de inicio
        if ($request->fecha_desde) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        // Filtro por fecha de fin
        if ($request->fecha_hasta) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $asistencias = $query->get();

        // Exportar PDF
        if ($tipo === 'pdf') {
            registrarBitacora(Auth::user(), 'reporte_asistencia', $request, 'Exportó reporte de asistencia en PDF');
            $pdf = PDF::loadView('admin.reportes.asistencia_pdf', compact('asistencias'));
            return $pdf->download('reporte_asistencia.pdf');
        }

        // Exportar Excel
        if ($tipo === 'excel') {
            registrarBitacora(Auth::user(), 'reporte_asistencia', $request, 'Exportó reporte de asistencia en Excel');
            return Excel::download(
                new \App\Exports\AsistenciaExport($asistencias),
                'reporte_asistencia.xlsx'
            );
        }

        return back()->with('error', 'Tipo de reporte no válido');
    }
    public function importarOferta(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new MaestrosOfertaImport, $request->file('archivo'));

        return back()->with('success', 'Oferta académica importada correctamente.');
    }
}