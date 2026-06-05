<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('user_id')->nullable();
        $table->text('description');
        $table->string('subject_type')->nullable();
        $table->uuid('subject_id')->nullable();
        $table->string('causer_type')->nullable();
        $table->uuid('causer_id')->nullable();
        $table->json('properties')->nullable();
        $table->string('ip_address', 45)->nullable();
        $table->timestamp('created_at')->nullable();

        $table->index(['user_id', 'created_at'], 'idx_act_user_created');
        $table->index(['subject_type', 'subject_id'], 'idx_act_subject');

        $table->foreign('user_id', 'fk_act_user')->references('id')->on('users')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};