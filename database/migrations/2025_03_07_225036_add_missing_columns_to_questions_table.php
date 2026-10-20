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
    Schema::table('questions', function (Blueprint $table) {
        $table->text('options')->nullable()->after('question_type');
        $table->string('correct_option')->nullable()->after('options');
    });
}

public function down()
{
    Schema::table('questions', function (Blueprint $table) {
        $table->dropColumn(['options', 'correct_option']);
    });
}

};
