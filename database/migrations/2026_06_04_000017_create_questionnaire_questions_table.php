<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('questionnaire_questions', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('questionnaire_id');
        $table->uuid('answer_type_id');
        $table->text('question_text');
        $table->smallInteger('question_order')->default(0);
        $table->boolean('is_required')->default(true);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();
        $table->uuid('created_by')->nullable();
        $table->uuid('updated_by')->nullable();
        $table->uuid('deleted_by')->nullable();

        $table->index(['questionnaire_id', 'question_order'], 'idx_qq_qnr_order');

        $table->foreign('questionnaire_id', 'fk_qq_qnr')->references('id')->on('questionnaires')->cascadeOnDelete();
        $table->foreign('answer_type_id', 'fk_qq_at')->references('id')->on('answer_types')->restrictOnDelete();
        $table->foreign('created_by', 'fk_qq_created_by')->references('id')->on('users')->nullOnDelete();
        $table->foreign('updated_by', 'fk_qq_updated_by')->references('id')->on('users')->nullOnDelete();
        $table->foreign('deleted_by', 'fk_qq_deleted_by')->references('id')->on('users')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_questions');
    }
};