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
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);

            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by', 'fk_appsetting_created_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->foreign('updated_by', 'fk_appsetting_updated_by')
                  ->references('id')->on('users')->onDelete('set null');

            $table->index('group', 'idx_appsetting_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
