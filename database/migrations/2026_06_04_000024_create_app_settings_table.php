<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('app_settings', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('group', 100);
        $table->string('key', 100);
        $table->text('value')->nullable();
        $table->enum('type', ['string', 'boolean', 'integer', 'json', 'password'])->default('string');
        $table->string('label', 255)->nullable();
        $table->text('description')->nullable();
        $table->boolean('is_encrypted')->default(false);
        $table->timestamps();
        $table->uuid('updated_by')->nullable();

        $table->unique(['group', 'key'], 'uq_app_settings_group_key');
        $table->index('group', 'idx_app_settings_group');

        $table->foreign('updated_by', 'fk_app_settings_updated_by')->references('id')->on('users')->nullOnDelete();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};