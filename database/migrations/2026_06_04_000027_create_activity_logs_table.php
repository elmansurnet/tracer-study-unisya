<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('user_id', 26)->nullable();
            $table->text('description')->nullable(false);
            $table->string('subject_type', 255)->nullable();
            $table->char('subject_id', 26)->nullable();
            $table->string('causer_type', 255)->nullable();
            $table->char('causer_id', 26)->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_actlog_user')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->index('user_id', 'idx_actlog_user');
            $table->index(['subject_type', 'subject_id'], 'idx_actlog_subject');
            $table->index(['causer_type', 'causer_id'], 'idx_actlog_causer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
