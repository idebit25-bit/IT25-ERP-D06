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
    Schema::table('pages', function (Blueprint $table) {
        $table->text('content')->nullable()->change();
    });
}

public function down()
{
    Schema::table('pages', function (Blueprint $table) {
        // Si quieres revertir el cambio, vuelve a la definición original
        $table->text('content')->nullable(false)->change();
    });
}

};
