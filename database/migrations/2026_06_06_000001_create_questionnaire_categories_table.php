<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_categories', function (Blueprint $table) {
            // PRIMARY KEY — UUID CHAR(36), konsisten dengan seluruh Phase 1-3
            $table->uuid('id')->primary();

            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Audit fields — FK ke users.id (UUID CHAR(36))
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_qcat_created_by');

            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_qcat_updated_by');

            $table->foreign('deleted_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_qcat_deleted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_categories');
    }
};
