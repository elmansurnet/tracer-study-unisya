<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            // PRIMARY KEY — UUID CHAR(36), konsisten dengan seluruh Phase 1-3
            $table->uuid('id')->primary();

            // FK ke questionnaire_categories.id (UUID CHAR(36))
            $table->uuid('questionnaire_category_id');
            $table->foreign('questionnaire_category_id')
                ->references('id')->on('questionnaire_categories')
                ->onDelete('restrict')
                ->name('fk_q_category');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('respondent_type', ['alumni', 'employer', 'both'])->default('alumni');
            $table->enum('scope', ['global', 'faculty', 'study_program'])->default('global');

            // FK kondisional berdasarkan scope
            // FK ke faculties.id (UUID CHAR(36))
            $table->uuid('faculty_id')->nullable();
            $table->foreign('faculty_id')
                ->references('id')->on('faculties')
                ->onDelete('set null')
                ->name('fk_q_faculty');

            // FK ke study_programs.id (UUID CHAR(36))
            $table->uuid('study_program_id')->nullable();
            $table->foreign('study_program_id')
                ->references('id')->on('study_programs')
                ->onDelete('set null')
                ->name('fk_q_study_program');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('version')->default(1)->unsigned();

            $table->timestamps();
            $table->softDeletes();

            // Audit fields — FK ke users.id (UUID CHAR(36))
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_q_created_by');

            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_q_updated_by');

            $table->foreign('deleted_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_q_deleted_by');

            // Indexes
            $table->index('questionnaire_category_id', 'idx_q_category');
            $table->index('respondent_type', 'idx_q_respondent_type');
            $table->index('scope', 'idx_q_scope');
            $table->index('is_active', 'idx_q_is_active');
            $table->index(['start_date', 'end_date'], 'idx_q_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
