<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_employment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('alumni_id');
            $table->uuid('institution_id')->nullable();
            $table->uuid('profession_id')->nullable();
            $table->string('job_title', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);

            $table->enum('salary_range', [
                '<1jt',
                '1-3jt',
                '3-5jt',
                '5-10jt',
                '>10jt',
            ])->nullable();

            $table->enum('job_relevance', [
                'sangat_relevan',
                'relevan',
                'kurang_relevan',
                'tidak_relevan',
            ])->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('alumni_id')
                ->references('id')->on('alumni')->cascadeOnDelete();

            $table->foreign('institution_id')
                ->references('id')->on('institutions')->nullOnDelete();

            $table->foreign('profession_id')
                ->references('id')->on('professions')->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')->on('users')->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')->on('users')->nullOnDelete();

            $table->foreign('deleted_by')
                ->references('id')->on('users')->nullOnDelete();

            $table->index(
                ['alumni_id', 'is_current', 'deleted_at'],
                'aeh_alumni_current_deleted_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_employment_histories');
    }
};