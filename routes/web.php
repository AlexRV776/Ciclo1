<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DocenteController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\Admin\HorarioMateriaController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN
|--------------------------------------------------------------------------
*/


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/password/forgot', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/password/forgot', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update.by.email');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect('/login');
});


/*
|--------------------------------------------------------------------------
| RUTAS DEL PANEL ADMINISTRADOR
|--------------------------------------------------------------------------
|
| Solo accesibles para usuarios autenticados y con rol_id = 1
|
*/
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');

Route::get('/usuario/definido', function () {
    $usuario = Auth::user();
    return view('usuario.definido', compact('usuario'));
})->name('usuario.definido')->middleware('auth');

Route::get('/usuario/sin-definir', function () {
    return view('usuario.sindefinir');
})->name('usuario.sindefinir')->middleware('auth');
use App\Http\Controllers\BitacoraController;

Route::get('/admin/bitacora', [BitacoraController::class, 'index'])
    ->name('admin.bitacora')
    ->middleware('auth');

Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('materias', MateriaController::class)->names('admin.materias');
});

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::resource('grupos', GrupoController::class);
});

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::resource('aulas', AulaController::class);
});

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::resource('horario', App\Http\Controllers\Admin\HorarioController::class);
});

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::resource('roles', App\Http\Controllers\Admin\RolController::class)
    ->parameters(['roles' => 'rol']);
}); 
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::resource('permisos', App\Http\Controllers\Admin\PermisoController::class);
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('docentes', DocenteController::class);
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/usuarios/{usuario}/contratar', [UsuarioController::class, 'contratar'])->name('admin.usuarios.contratar');
});

// Panel general
// 🧑 Panel del usuario autenticado (docente, admin, etc.)
Route::get('/panel', [UsuarioController::class, 'panel'])
    ->name('usuario.panel')
    ->middleware('auth');

// 👑 Sección de administración
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // 📋 Listado de todos los usuarios
    Route::get('/usuarios', [UsuarioController::class, 'gestionarUsuario'])
        ->name('admin.usuarios.index');

    // 🧾 Formulario de contratación de un usuario
    Route::get('/usuarios/{usuario}/contratar', [UsuarioController::class, 'contratar'])
        ->name('admin.usuarios.contratar');

    // 💼 Asignar rol a un usuario
    Route::post('/usuarios/{usuario}/asignar', [UsuarioController::class, 'asignarRol'])
        ->name('admin.usuarios.asignarRol');

    // 🧑‍🏫 Guardar datos del docente (solo admin)
    Route::post('/usuarios/{usuario}/docente', [UsuarioController::class, 'guardarDocenteAdmin'])
        ->name('admin.docente.guardar');


});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::resource('grupo_materia', App\Http\Controllers\Admin\GrupoMateriaController::class)
        ->names('admin.grupo_materia');
});

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::resource('horario_materia', App\Http\Controllers\Admin\HorarioMateriaController::class);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::get('/asistencia/marcar/{id}', [AsistenciaController::class, 'marcar'])->name('asistencia.marcar');
    Route::post('/asistencia/guardar/{id}', [AsistenciaController::class, 'guardar'])->name('asistencia.guardar');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/asistencia/gestionar', [AsistenciaController::class, 'gestionar'])->name('asistencia.gestionar');
    Route::post('/asistencia/gestionar', [AsistenciaController::class, 'filtrar'])->name('asistencia.filtrar');
});


use App\Http\Controllers\PerfilController;

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::post('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
});

use App\Http\Controllers\ReservaAulaController;

Route::middleware(['auth'])->group(function () {

    Route::get('/reservas', [ReservaAulaController::class, 'index'])->name('reservas.index');

    Route::post('/reservas/disponibles', [ReservaAulaController::class, 'disponibles'])->name('reservas.disponibles');

    // ✅ NUEVO: formulario de confirmación
    Route::post('/reservas/confirmar', [ReservaAulaController::class, 'confirmar'])
        ->name('reservas.confirmar');
    Route::get('/reservas/confirmar', function () {
        return redirect()->route('reservas.index');
    });
    Route::post('/reservas/crear', [ReservaAulaController::class, 'reservar'])->name('reservas.crear');

    // ✅ Nueva vista para ver todas las reservas
    Route::get('/reservas/listado', [ReservaAulaController::class, 'listado'])
        ->name('reservas.listado');
});
Route::middleware(['auth'])->group(function () {
    
    // Ruta del índice de reportes
    Route::get('/admin/reportes', function () {
        return view('admin.reportes.index');
    })->name('admin.reportes.index');

    Route::get('/admin/reportes/personal', [App\Http\Controllers\ReporteController::class, 'personal'])
        ->name('admin.reportes.personal');

    Route::post('/admin/reportes/personal/export', [App\Http\Controllers\ReporteController::class, 'exportPersonal'])
        ->name('admin.reportes.personal.export');

    Route::get('/admin/reportes/asistencia', [App\Http\Controllers\ReporteController::class, 'asistencia'])
        ->name('admin.reportes.asistencia');
        
    Route::post('/admin/reportes/asistencia/export', [App\Http\Controllers\ReporteController::class, 'exportAsistencia'])
        ->name('admin.reportes.asistencia.export');
});
use App\Http\Controllers\Admin\UsuarioImportController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/usuario/importar', [UsuarioImportController::class, 'showImportForm'])
        ->name('admin.usuario.importar');
    Route::post('/usuario/importar', [UsuarioImportController::class, 'import'])
        ->name('admin.usuario.importar.post');
});
use App\Http\Controllers\CalendarioController;

Route::middleware(['auth'])->group(function () {
    Route::get('/docente/calendario', [CalendarioController::class, 'index'])
        ->name('docente.calendario');
});
Route::post('/admin/oferta/importar', [App\Http\Controllers\ReporteController::class, 'importarOferta'])
    ->name('oferta.importar');
    use App\Http\Controllers\MaestrosOfertaController;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/oferta/importar', [MaestrosOfertaController::class, 'index'])->name('oferta.importar');
    Route::post('/admin/oferta/importar', [MaestrosOfertaController::class, 'importar'])->name('oferta.importar.post');
});