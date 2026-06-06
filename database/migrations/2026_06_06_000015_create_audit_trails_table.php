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
            $table->uuid('user_id')->nullable()->comment('FK ke users.id — nullable for system events');
            $table->string('user_type', 50)->nullable();
            $table->string('event', 50);
            $table->string('auditable_type', 100);
            $table->uuid('auditable_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id', 'fk_audit_user')
                  ->references('id')->on('users')->onDelete('set null');

            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_auditable');
            $table->index('user_id', 'idx_audit_user');
            $table->index('event', 'idx_audit_event');
            $table->index('created_at', 'idx_audit_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
