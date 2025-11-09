<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->id();

            // Fecha de la asistencia
            $table->date('fecha');

            // Modalidad
            $table->enum('modalidad', ['presencial', 'virtual']);

            // Estado (pendiente, confirmado, falta)
            // PostgreSQL permite enum de Laravel sin problema
            $table->enum('estado', ['pendiente', 'confirmado', 'falta'])
                  ->default('pendiente');

            // Docente que marcó asistencia
            $table->integer('docente_registro');
            $table->foreign('docente_registro')
                ->references('registro')
                ->on('docente')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Relación con horario_materia
            $table->foreignId('horario_materia_id')
                ->constrained('horario_materia')
                ->onDelete('cascade');
            
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia', function (Blueprint $table) {
            $table->dropForeign(['docente_registro']);
            $table->dropForeign(['horario_materia_id']);
        });

        Schema::dropIfExists('asistencia');
    }
};
