<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->text('content')->change(); // Asegurar que content puede guardar HTML
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('content')->change(); // Volver a string si se revierte
        });
    }
};

