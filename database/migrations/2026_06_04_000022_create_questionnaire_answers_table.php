<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('questionnaire_response_id', 26)->nullable(false);
            $table->json('question_snapshot')->nullable(false);
            $table->json('answer_type_snapshot')->nullable(false);
            $table->string('answer_value', 255)->nullable(false);
            $table->timestamps();

            $table->foreign('questionnaire_response_id', 'fk_qans_response')
                ->references('id')->on('questionnaire_responses')
                ->onDelete('cascade');

            $table->index('questionnaire_response_id', 'idx_qans_response');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
