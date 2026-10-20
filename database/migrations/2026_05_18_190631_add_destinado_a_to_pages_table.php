<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $blueprint) {
            // Agrega la columna destinado_a justo después de la columna 'order'
            $blueprint->enum('destinado_a', ['Colaborador', 'Estadía', 'Ambos'])->default('Ambos')->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $blueprint) {
            // Si necesitas revertir la migración, elimina la columna
            $blueprint->dropColumn('destinado_a');
        });
    }
};