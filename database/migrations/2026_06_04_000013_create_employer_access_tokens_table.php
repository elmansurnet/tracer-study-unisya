<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('alumni_id');
            $table->uuid('institution_id');
            $table->string('contact_name', 255);
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->string('token', 64)->unique('eat_token_unique');
            $table->string('token_plain', 64)->nullable();
            $table->boolean('is_used')->default(false);
            $table->boolean('is_revoked')->default(false);
            $table->boolean('otp_verified')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->uuid('tracer_study_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->uuid('revoked_by')->nullable();

            $table->foreign('alumni_id', 'eat_alumni_fk')
                ->references('id')
                ->on('alumni')
                ->cascadeOnDelete();

            $table->foreign('institution_id', 'eat_institution_fk')
                ->references('id')
                ->on('institutions')
                ->restrictOnDelete();

            $table->foreign('created_by', 'eat_created_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by', 'eat_updated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('deleted_by', 'eat_deleted_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('revoked_by', 'eat_revoked_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['token', 'is_used', 'is_revoked', 'expires_at'], 'eat_token_status_exp_idx');
            $table->index(['alumni_id', 'institution_id', 'is_used', 'is_revoked'], 'eat_alumni_inst_status_idx');
            $table->index('deleted_at', 'eat_deleted_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_access_tokens');
    }
};