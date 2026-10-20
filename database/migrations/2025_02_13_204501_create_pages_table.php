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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');       // Título de la página
            $table->string('slug')->unique(); // URL amigable
            $table->text('content')->nullable()->change();       // Contenido HTML
            $table->string('video_url')->nullable(); // Enlace de YouTube (opcional)
            $table->integer('order')->default(0);    // Orden para reordenar páginas
            $table->string('attachment')->nullable(); // Ruta del archivo adjunto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
