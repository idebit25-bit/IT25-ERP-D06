<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('content_type', ['video', 'document', 'quiz']);
            $table->string('content_url')->nullable();
            $table->integer('order')->default(1);
            $table->unsignedBigInteger('dependency_id')->nullable();
            $table->foreign('dependency_id')->references('id')->on('steps')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('steps');
    }
};
