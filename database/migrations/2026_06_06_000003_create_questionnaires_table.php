<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('questionnaire_category_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('respondent_type', ['alumni', 'employer', 'both']);
            $table->enum('scope', ['global', 'faculty', 'study_program']);
            $table->ulid('faculty_id')->nullable();
            $table->ulid('study_program_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->ulid('created_by')->nullable();
            $table->ulid('updated_by')->nullable();
            $table->ulid('deleted_by')->nullable();

            $table->foreign('questionnaire_category_id')->references('id')->on('questionnaire_categories')->restrictOnDelete();
            $table->foreign('faculty_id')->references('id')->on('faculties')->nullOnDelete();
            $table->foreign('study_program_id')->references('id')->on('study_programs')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['respondent_type', 'is_active', 'deleted_at'], 'idx_qnr_resp_active');
            $table->index(['scope', 'faculty_id', 'study_program_id'], 'idx_qnr_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
