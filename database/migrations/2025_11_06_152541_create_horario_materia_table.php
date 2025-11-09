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
        Schema::create('horario_materia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('horario_id')->constrained('horario')->onDelete('cascade');
            $table->foreignId('grupo_materia_id')->constrained('grupo_materia')->onDelete('cascade');

            $table->integer('nro');
            $table->foreign('nro')
                ->references('nro')
                ->on('aulas')
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
        Schema::dropIfExists('horario_materia');
    }
};
