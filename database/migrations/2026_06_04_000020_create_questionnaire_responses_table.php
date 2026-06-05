<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('questionnaire_responses', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tracer_study_id')->nullable();
        $table->uuid('questionnaire_id');
        $table->enum('respondent_type', ['alumni', 'employer']);
        $table->uuid('alumni_id')->nullable();
        $table->uuid('employer_access_token_id')->nullable();
        $table->timestamp('submitted_at');
        $table->string('ip_address', 45)->nullable();
        $table->json('questionnaire_snapshot');
        $table->timestamps();

        $table->unique(['tracer_study_id', 'questionnaire_id', 'alumni_id'], 'uq_qres_alumni');
        $table->unique(['tracer_study_id', 'questionnaire_id', 'employer_access_token_id'], 'uq_qres_employer');
        $table->index(['tracer_study_id', 'questionnaire_id', 'respondent_type'], 'idx_qres_ts_qnr_resp');
        $table->index('alumni_id', 'idx_qres_alumni');

        $table->foreign('tracer_study_id', 'fk_qres_ts')->references('id')->on('tracer_studies')->nullOnDelete();
        $table->foreign('questionnaire_id', 'fk_qres_qnr')->references('id')->on('questionnaires')->restrictOnDelete();
        $table->foreign('alumni_id', 'fk_qres_alumni')->references('id')->on('alumni')->nullOnDelete();
        $table->foreign('employer_access_token_id', 'fk_qres_eat')->references('id')->on('employer_access_tokens')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};