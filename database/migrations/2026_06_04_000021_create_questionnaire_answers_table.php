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
        $table->uuid('questionnaire_response_id');
        $table->json('question_snapshot');
        $table->json('answer_type_snapshot');
        $table->string('answer_value', 255);
        $table->timestamps();

        $table->index('questionnaire_response_id', 'idx_qans_qres');

        $table->foreign('questionnaire_response_id', 'fk_qans_qres')
            ->references('id')
            ->on('questionnaire_responses')
            ->cascadeOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};