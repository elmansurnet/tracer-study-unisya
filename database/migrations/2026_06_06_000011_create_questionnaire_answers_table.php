<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('response_id')->comment('FK ke questionnaire_responses.id');
            $table->uuid('question_id')->comment('FK ke questionnaire_questions.id');

            $table->text('answer_text')->nullable();
            $table->json('answer_options')->nullable()->comment('Untuk tipe checkbox / multi-select');
            $table->tinyInteger('answer_scale')->nullable()->comment('Untuk tipe scale 1-10');

            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('response_id', 'fk_qans_response')
                  ->references('id')->on('questionnaire_responses')->onDelete('cascade');

            $table->foreign('question_id', 'fk_qans_question')
                  ->references('id')->on('questionnaire_questions')->onDelete('cascade');

            $table->foreign('created_by', 'fk_qans_created_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->foreign('updated_by', 'fk_qans_updated_by')
                  ->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('response_id', 'idx_qans_response');
            $table->index('question_id', 'idx_qans_question');
            $table->unique(['response_id', 'question_id'], 'uq_qans_resp_question');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
