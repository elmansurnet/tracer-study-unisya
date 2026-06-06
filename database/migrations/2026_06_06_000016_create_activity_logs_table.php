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
            $table->uuid('user_id')->nullable()->comment('FK ke users.id');
            $table->string('action', 100);
            $table->string('subject_type', 100)->nullable();
            $table->uuid('subject_id')->nullable();
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id', 'fk_actlog_user')
                  ->references('id')->on('users')->onDelete('set null');

            $table->index(['subject_type', 'subject_id'], 'idx_actlog_subject');
            $table->index('user_id', 'idx_actlog_user');
            $table->index('action', 'idx_actlog_action');
            $table->index('created_at', 'idx_actlog_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
