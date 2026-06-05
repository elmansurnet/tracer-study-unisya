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
        $table->enum('channel', ['whatsapp', 'email']);
        $table->string('recipient');
        $table->string('subject')->nullable();
        $table->text('message');
        $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
        $table->json('provider_response')->nullable();
        $table->text('error_message')->nullable();
        $table->unsignedTinyInteger('retries')->default(0);
        $table->timestamp('sent_at')->nullable();
        $table->string('reference_type', 100)->nullable();
        $table->uuid('reference_id')->nullable();
        $table->timestamps();

        $table->index(['channel', 'status'], 'idx_nlog_channel_status');
        $table->index(['reference_type', 'reference_id'], 'idx_nlog_ref');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};