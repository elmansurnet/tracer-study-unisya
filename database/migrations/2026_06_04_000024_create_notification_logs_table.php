<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->enum('channel', ['whatsapp', 'email'])->nullable(false);
            $table->string('recipient', 255)->nullable(false);
            $table->string('subject', 255)->nullable();
            $table->text('message')->nullable(false);
            $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->tinyInteger('retries')->unsigned()->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->string('reference_type', 100)->nullable();
            $table->char('reference_id', 26)->nullable();
            $table->timestamps();

            $table->index('channel', 'idx_notif_logs_channel');
            $table->index('status', 'idx_notif_logs_status');
            $table->index('reference_type', 'idx_notif_logs_ref_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
