<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))

    // Archivos de rutas
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Middleware
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'permiso' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })

    // Registrar EXCEPCIONES
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    // ✅ Registrar comandos personalizados
    ->withCommands([
        App\Console\Commands\MarcarFaltas::class, // <--- AQUI
    ])

    // ✅ Registrar el scheduler
    ->withSchedule(function (Schedule $schedule) {
        // Ejecuta la tarea cada minuto
        $schedule->command('asistencia:marcar-faltas')->everyMinute();
    })

    ->create();