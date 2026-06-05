<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('identifier', 255);        // email atau nomor WA
            $table->enum('identifier_type', ['email', 'whatsapp']);
            $table->string('otp_code', 10);           // bcrypt hash di app layer
            $table->enum('purpose', [
                'login',
                'employer_access',
                'phone_verify',
                'email_verify',
            ]);
            $table->uuid('reference_id')->nullable();  // employer_access_tokens.id, dsb
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(5);
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['identifier', 'purpose', 'expires_at', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};