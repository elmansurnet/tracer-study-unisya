<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique()->nullable();
            $table->uuid('study_program_id');
            $table->string('nim', 50)->unique();
            $table->string('name');
            // ✅ Enum gender final: laki_laki, perempuan (underscore)
            $table->enum('gender', ['laki_laki', 'perempuan']);
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->year('graduation_year');
            $table->date('graduation_date')->nullable();
            $table->decimal('ipk', 4, 2)->nullable();
            $table->text('thesis_title')->nullable();
            $table->string('photo', 255)->nullable();
            $table->boolean('is_employed')->default(false);
            // ✅ Enum employment_status final: underscore konsisten
            $table->enum('employment_status', [
                'bekerja',
                'wirausaha',
                'melanjutkan_studi',
                'belum_bekerja',
            ])->nullable();
            $table->unsignedSmallInteger('waiting_period_months')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('study_program_id')
                  ->references('id')->on('study_programs')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['study_program_id', 'graduation_year']);
            $table->index(['employment_status', 'is_employed']);
            $table->index('graduation_year');
            $table->index(['nim']);
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};