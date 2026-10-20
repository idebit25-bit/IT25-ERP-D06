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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default('colaborador'); // colaborador o estadia
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('target_profile')->default('todos'); // colaborador, estadia, o todos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_and_pages', function (Blueprint $table) {
            //
        });
    }
};
