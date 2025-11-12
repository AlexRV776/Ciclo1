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
        Schema::create('docente', function (Blueprint $table) {
            // ✅ La PK es el registro del usuario
            $table->integer('registro')->primary();

            $table->date('fecha_contrato');
            $table->string('especialidad', 100);
            $table->decimal('sueldo', 10, 2);
            $table->integer('carga_horaria_max')->default(40);    
            // ✅ Relación 1 a 1 con usuario
            $table->foreign('registro')
                ->references('registro')
                ->on('usuarios')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente');
    }
};
