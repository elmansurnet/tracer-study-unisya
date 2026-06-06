<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('user_id', 26)->nullable();
            $table->enum('user_type', ['user', 'employer', 'system'])->nullable();
            $table->string('event', 50)->nullable(false);
            $table->string('auditable_type', 255)->nullable(false);
            $table->char('auditable_id', 26)->nullable(false);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('url', 1000)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', 'fk_audit_user')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->index('user_id', 'idx_audit_user');
            $table->index('auditable_type', 'idx_audit_type');
            $table->index('auditable_id', 'idx_audit_id');
            $table->index('event', 'idx_audit_event');
            $table->index('created_at', 'idx_audit_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
