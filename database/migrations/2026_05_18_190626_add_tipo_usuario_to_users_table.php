<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            // Agrega la columna tipo_usuario justo después de la columna 'role'
            $blueprint->enum('tipo_usuario', ['Colaborador', 'Estadía'])->default('Colaborador')->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            // Si necesitas revertir la migración, elimina la columna
            $blueprint->dropColumn('tipo_usuario');
        });
    }
};