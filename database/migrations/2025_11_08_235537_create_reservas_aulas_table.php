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
        Schema::create('reservas_aulas', function (Blueprint $table) {
            $table->id();

            // docente que solicita
            $table->integer('usuario_registro');
            $table->foreign('usuario_registro')
                ->references('registro')
                ->on('usuarios')
                ->onDelete('cascade');

            // aula solicitada
            $table->unsignedBigInteger('aula_id');
            $table->foreign('aula_id')->references('nro')->on('aulas')->onDelete('cascade');

            // fecha y horario
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            // Estado de la reserva
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])
                ->default('pendiente');

            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas_aulas');
    }
};
