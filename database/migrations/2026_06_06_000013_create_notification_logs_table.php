<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->comment('FK ke users.id');
            $table->string('channel', 50)->comment('email, whatsapp, push');
            $table->string('type', 100);
            $table->text('message');
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_notif_log_user')
                  ->references('id')->on('users')->onDelete('set null');

            $table->index(['user_id', 'channel'], 'idx_notif_log_user_channel');
            $table->index('status', 'idx_notif_log_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
