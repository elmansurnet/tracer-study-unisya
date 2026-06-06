<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('tracer_study_id', 26)->nullable();
            $table->char('questionnaire_id', 26)->nullable(false);
            $table->enum('respondent_type', ['alumni', 'employer'])->nullable(false);
            $table->char('alumni_id', 26)->nullable();
            $table->char('employer_access_token_id', 26)->nullable();
            $table->timestamp('submitted_at')->nullable(false);
            $table->string('ip_address', 45)->nullable();
            $table->json('questionnaire_snapshot')->nullable(false);
            $table->timestamps();

            $table->foreign('tracer_study_id', 'fk_qresp_tracer_study')
                ->references('id')->on('tracer_studies')
                ->onDelete('set null');

            $table->foreign('questionnaire_id', 'fk_qresp_questionnaire')
                ->references('id')->on('questionnaires')
                ->onDelete('cascade');

            $table->foreign('alumni_id', 'fk_qresp_alumni')
                ->references('id')->on('alumni')
                ->onDelete('set null');

            $table->foreign('employer_access_token_id', 'fk_qresp_employer_token')
                ->references('id')->on('employer_access_tokens')
                ->onDelete('set null');

            $table->unique(
                ['tracer_study_id', 'questionnaire_id', 'alumni_id'],
                'uq_qresp_tracer_questionnaire_alumni'
            );

            $table->unique(
                ['tracer_study_id', 'questionnaire_id', 'employer_access_token_id'],
                'uq_qresp_tracer_questionnaire_employer'
            );

            $table->index('tracer_study_id', 'idx_qresp_tracer_study');
            $table->index('questionnaire_id', 'idx_qresp_questionnaire');
            $table->index('respondent_type', 'idx_qresp_respondent_type');
            $table->index('alumni_id', 'idx_qresp_alumni');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};
