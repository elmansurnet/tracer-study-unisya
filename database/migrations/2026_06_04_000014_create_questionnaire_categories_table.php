<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('questionnaire_categories', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name');
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();
        $table->uuid('created_by')->nullable();
        $table->uuid('updated_by')->nullable();
        $table->uuid('deleted_by')->nullable();

        $table->index('is_active', 'idx_qc_is_active');

        $table->foreign('created_by', 'fk_qc_created_by')->references('id')->on('users')->nullOnDelete();
        $table->foreign('updated_by', 'fk_qc_updated_by')->references('id')->on('users')->nullOnDelete();
        $table->foreign('deleted_by', 'fk_qc_deleted_by')->references('id')->on('users')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_categories');
    }
};