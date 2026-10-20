<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Algunas instalaciones ya recibieron esta columna mediante SQL manual.
        if (!Schema::hasColumn('users', 'employee_code')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('employee_code')->nullable());
        }
        if (!Schema::hasIndex('users', ['employee_code'], 'unique')) {
            Schema::table('users', fn (Blueprint $table) => $table->unique('employee_code'));
        }
    }

    public function down(): void
    {
        // Conservar los códigos, incluso si fueron incorporados antes de esta migración.
        throw new RuntimeException('Esta migración conserva identificadores compartidos. Su reversión requiere revisión manual.');
    }
};
