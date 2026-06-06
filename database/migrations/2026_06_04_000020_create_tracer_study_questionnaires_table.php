<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracer_study_questionnaires', function (Blueprint $table) {
            $table->char('tracer_study_id', 26)->nullable(false);
            $table->char('questionnaire_id', 26)->nullable(false);
            $table->smallInteger('order')->default(0);
            $table->timestamps();

            $table->primary(['tracer_study_id', 'questionnaire_id']);

            $table->foreign('tracer_study_id', 'fk_tsq_tracer_study')
                ->references('id')->on('tracer_studies')
                ->onDelete('cascade');

            $table->foreign('questionnaire_id', 'fk_tsq_questionnaire')
                ->references('id')->on('questionnaires')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_study_questionnaires');
    }
};
