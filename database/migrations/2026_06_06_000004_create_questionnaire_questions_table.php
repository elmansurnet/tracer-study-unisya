<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_questions', function (Blueprint $table) {
            // PRIMARY KEY — UUID CHAR(36), konsisten dengan seluruh Phase 1-3
            $table->uuid('id')->primary();

            // FK ke questionnaires.id (UUID CHAR(36))
            $table->uuid('questionnaire_id');
            $table->foreign('questionnaire_id')
                ->references('id')->on('questionnaires')
                ->onDelete('cascade')
                ->name('fk_qq_questionnaire');

            // FK ke answer_types.id (UUID CHAR(36))
            $table->uuid('answer_type_id');
            $table->foreign('answer_type_id')
                ->references('id')->on('answer_types')
                ->onDelete('restrict')
                ->name('fk_qq_answer_type');

            $table->text('question_text');
            $table->smallInteger('question_order')->default(0)->unsigned();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Audit fields — FK ke users.id (UUID CHAR(36))
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_qq_created_by');

            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_qq_updated_by');

            $table->foreign('deleted_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_qq_deleted_by');

            // Indexes
            $table->index('questionnaire_id', 'idx_qq_questionnaire');
            $table->index('answer_type_id', 'idx_qq_answer_type');
            $table->index(['questionnaire_id', 'question_order'], 'idx_qq_order');
            $table->index('is_active', 'idx_qq_is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_questions');
    }
};
