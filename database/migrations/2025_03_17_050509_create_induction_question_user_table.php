<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInductionQuestionUserTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up()
    {
        Schema::create('induction_question_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('question_id');
            $table->timestamps();

            // Definir las claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');

            // Evitar duplicados
            $table->unique(['user_id', 'question_id']);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down()
    {
        Schema::dropIfExists('induction_question_user');
    }
}
