<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            // Campo para almacenar la ruta del archivo
            $table->string('attachment')->nullable()->after('video_url');
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};

