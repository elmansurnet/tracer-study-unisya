<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('tracer_study_questionnaires', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('tracer_study_id');
        $table->uuid('questionnaire_id');
        $table->smallInteger('order')->default(0);
        $table->timestamps();

        $table->unique(['tracer_study_id', 'questionnaire_id'], 'uq_tsq_ts_qnr');

        $table->foreign('tracer_study_id', 'fk_tsq_ts')->references('id')->on('tracer_studies')->cascadeOnDelete();
        $table->foreign('questionnaire_id', 'fk_tsq_qnr')->references('id')->on('questionnaires')->restrictOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('tracer_study_questionnaires');
    }
};