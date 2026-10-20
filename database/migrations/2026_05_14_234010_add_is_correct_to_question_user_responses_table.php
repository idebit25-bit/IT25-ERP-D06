<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Se cambió 'Bundle' por 'Blueprint' para que Laravel reconozca el tipo de dato
        Schema::table('question_user_responses', function (Blueprint $table) {
            // Almacenará 1 si es correcta, 0 si es incorrecta, null para abiertas
            $table->boolean('is_correct')->nullable()->after('answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_user_responses', function (Blueprint $table) {
            // Eliminamos la columna si se hace un rollback
            $table->dropColumn('is_correct');
        });
    }
};