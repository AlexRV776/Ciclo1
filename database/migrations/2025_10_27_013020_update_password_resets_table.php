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
        Schema::table('password_resets', function (Blueprint $table) {
    if (!Schema::hasColumn('password_resets', 'correo')) {
        $table->string('correo')->unique();
    }
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
