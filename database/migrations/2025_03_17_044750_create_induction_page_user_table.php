<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInductionPageUserTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up()
    {
        Schema::create('induction_page_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('page_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down()
    {
        Schema::dropIfExists('induction_page_user');
    }
}
