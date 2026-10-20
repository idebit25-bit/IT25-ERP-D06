<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('type')->default('page'); 
            $table->string('question_type')->nullable();
            $table->text('options')->nullable();
            $table->string('correct_option')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['type', 'question_type', 'options', 'correct_option']);
        });
    }
};

