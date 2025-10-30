<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Solo agregar si la columna no existe
            if (!Schema::hasColumn('usuarios', 'estado')) {
                $table->string('estado')->default('pendiente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
