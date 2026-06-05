<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('faculty_id', 36);
            $table->string('code', 20)->unique();
            $table->string('name', 255);
            $table->enum('degree_level', ['D3', 'S1', 'S2', 'S3', 'Profesi']);
            $table->text('description')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign('faculty_id')->references('id')->on('faculties')->restrictOnDelete();
            $table->index(['faculty_id', 'is_active', 'deleted_at']);
            $table->index('degree_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
