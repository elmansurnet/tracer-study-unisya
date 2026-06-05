<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('academic_year', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'aktif', 'selesai', 'dibatalkan'])->default('draft');
            $table->enum('target_scope', ['all', 'faculty', 'study_program'])->default('all');
            $table->uuid('target_faculty_id')->nullable();
            $table->uuid('target_study_program_id')->nullable();
            $table->json('target_graduation_years')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('target_faculty_id', 'ts_target_faculty_fk')
                ->references('id')
                ->on('faculties')
                ->nullOnDelete();

            $table->foreign('target_study_program_id', 'ts_target_study_program_fk')
                ->references('id')
                ->on('study_programs')
                ->nullOnDelete();

            $table->foreign('created_by', 'ts_created_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by', 'ts_updated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('deleted_by', 'ts_deleted_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['status', 'start_date', 'end_date'], 'ts_status_date_idx');
            $table->index(
                ['target_scope', 'target_faculty_id', 'target_study_program_id'],
                'ts_target_scope_filter_idx'
            );
            $table->index('deleted_at', 'ts_deleted_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_studies');
    }
};