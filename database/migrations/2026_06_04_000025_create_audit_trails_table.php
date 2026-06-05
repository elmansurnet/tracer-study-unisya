<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('audit_trails', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('user_id')->nullable();
        $table->enum('user_type', ['user', 'employer', 'system'])->nullable();
        $table->string('event', 50);
        $table->string('auditable_type');
        $table->uuid('auditable_id');
        $table->json('old_values')->nullable();
        $table->json('new_values')->nullable();
        $table->string('url', 1000)->nullable();
        $table->string('ip_address', 45)->nullable();
        $table->string('user_agent', 500)->nullable();
        $table->timestamp('created_at')->nullable();

        $table->index(['user_id', 'event'], 'idx_audit_user_event');
        $table->index(['auditable_type', 'auditable_id'], 'idx_audit_subject');
        $table->index('created_at', 'idx_audit_created_at');

        $table->foreign('user_id', 'fk_audit_user')->references('id')->on('users')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};