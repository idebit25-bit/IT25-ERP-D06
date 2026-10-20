<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderColumnToQuestionsTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Se agrega la columna "order" (puedes ajustar la posición con ->after())
            $table->integer('order')->default(0)->after('question_type');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
