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
        Schema::create('grupo_materia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grupo_id')->constrained('grupo')->onDelete('cascade');
            $table->string('materia_sigla', 10);
            $table->foreign('materia_sigla')->references('sigla')->on('materia')->onDelete('cascade');
            // 👇 Primero defines la columna
            $table->integer('docente_registro');
            // ✅ FOREIGN KEY A DOCENTE
            $table->foreign('docente_registro')
                ->references('registro')
                ->on('docente')
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
        Schema::dropIfExists('grupo_materia');
    }
};
