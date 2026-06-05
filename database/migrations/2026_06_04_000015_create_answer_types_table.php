<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('answer_types', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('code', 50);
        $table->string('name', 100);
        $table->text('description')->nullable();
        $table->json('config')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->uuid('created_by')->nullable();
        $table->uuid('updated_by')->nullable();

        $table->unique('code', 'uq_at_code');
        $table->index('is_active', 'idx_at_is_active');

        $table->foreign('created_by', 'fk_at_created_by')->references('id')->on('users')->nullOnDelete();
        $table->foreign('updated_by', 'fk_at_updated_by')->references('id')->on('users')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('answer_types');
    }
};