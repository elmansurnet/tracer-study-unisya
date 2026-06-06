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

            $table->uuid('questionnaire_id')->comment('FK ke questionnaires.id');
            $table->uuid('alumni_id')->comment('FK ke alumni.id');
            $table->uuid('tracer_study_id')->nullable()->comment('FK ke tracer_studies.id');

            $table->enum('status', ['draft', 'submitted', 'validated'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->uuid('validated_by')->nullable()->comment('FK ke users.id');

            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('questionnaire_id', 'fk_qresp_questionnaire')
                  ->references('id')->on('questionnaires')->onDelete('cascade');

            $table->foreign('alumni_id', 'fk_qresp_alumni')
                  ->references('id')->on('alumni')->onDelete('cascade');

            $table->foreign('tracer_study_id', 'fk_qresp_tracer_study')
                  ->references('id')->on('tracer_studies')->onDelete('set null');

            $table->foreign('validated_by', 'fk_qresp_validated_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->foreign('created_by', 'fk_qresp_created_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->foreign('updated_by', 'fk_qresp_updated_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->foreign('deleted_by', 'fk_qresp_deleted_by')
                  ->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['questionnaire_id', 'alumni_id'], 'idx_qresp_q_alumni');
            $table->index('status', 'idx_qresp_status');
            $table->index('alumni_id', 'idx_qresp_alumni');
            $table->unique(['questionnaire_id', 'alumni_id', 'deleted_at'], 'uq_qresp_q_alumni');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};
