<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Asistencia;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Imports\MaestrosOfertaImport;

class ReporteController extends Controller
{
    public function personal(Request $request)
    {
        $roles = \App\Models\Rol::all();
        return view('admin.reportes.personal', compact('roles'));
    }

    public function exportPersonal(Request $request)
    {
        $tipo = $request->input('tipo');

        $query = Usuario::select('registro','nombre','correo','rol_id')->with('rol');

        if ($request->rol_id) {
            $query->where('rol_id', $request->rol_id);
        }

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

        if ($tipo === 'pdf') {
            $pdf = PDF::loadView('admin.reportes.personal_pdf', compact('personal'));
            return $pdf->download('reporte_personal.pdf');
        }

        if ($tipo === 'excel') {
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
        return view('admin.reportes.asistencia', compact('docentes'));
    }

    public function grupos()
    {
        return view('admin.reportes.grupos');
    }

    public function exportGrupos(Request $request)
    {
        $tipo = $request->input('tipo');
        $grupos = Grupo::orderBy('id')->get();

        if ($tipo === 'pdf') {
            $pdf = PDF::loadView('admin.reportes.grupos_pdf', compact('grupos'));
            return $pdf->download('reporte_grupos.pdf');
        }

        if ($tipo === 'excel') {
            return Excel::download(new \App\Exports\GruposExport($grupos), 'reporte_grupos.xlsx');
        }

        return back()->with('error', 'Tipo de reporte no válido');
    }

    public function exportAsistencia(Request $request)
    {
        $tipo = $request->input('tipo');
        $query = Asistencia::with(['docente.usuario', 'horarioMateria.grupoMateria.materia'])
            ->orderBy('fecha', 'desc');

        if ($request->docente_registro) {
            $query->where('docente_registro', $request->docente_registro);
        }

        if ($request->fecha_desde) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->fecha_hasta) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $asistencias = $query->get();

        if ($tipo === 'pdf') {
            $pdf = PDF::loadView('admin.reportes.asistencia_pdf', compact('asistencias'));
            return $pdf->download('reporte_asistencia.pdf');
        }

        if ($tipo === 'excel') {
            return Excel::download(
                new \App\Exports\AsistenciaExport($asistencias),
                'reporte_asistencia.xlsx'
            );
        }

        return back()->with('error', 'Tipo de reporte no válido');
    }

    public function materias()
    {
        $semestres = Materia::select('semestre')->distinct()->orderBy('semestre')->get();
        return view('admin.reportes.materias', compact('semestres'));
    }


    public function exportMaterias(Request $request)
    {
        $tipo = $request->input('tipo');
        $query = Materia::query()->orderBy('sigla');

        if ($request->semestre) {
            $query->where('semestre', $request->semestre);
        }

        $materias = $query->get();

        if ($tipo === 'pdf') {
            $pdf = PDF::loadView('admin.reportes.materias_pdf', compact('materias'));
            return $pdf->download('reporte_materias.pdf');
        }

        if ($tipo === 'excel') {
            return Excel::download(new \App\Exports\MateriasExport($materias), 'reporte_materias.xlsx');
        }


        return back()->with('error', 'Tipo de reporte no válido');
    }

    public function importarOferta(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new MaestrosOfertaImport, $request->file('archivo'));

        //Registrar bitácora
        registrarBitacora(Auth::user(), 'importar_oferta', $request, 'Importó una nueva oferta académica desde un archivo Excel'
        );

        return back()->with('success', 'Oferta académica importada correctamente.');
    }
}
