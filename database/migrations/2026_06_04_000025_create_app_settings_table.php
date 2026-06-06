<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('group', 100)->nullable(false);
            $table->string('key', 100)->nullable(false);
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'boolean', 'integer', 'json', 'password'])->default('string');
            $table->string('label', 255)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_encrypted')->unsigned()->default(0);
            $table->timestamps();
            $table->char('updated_by', 26)->nullable();

            $table->unique(['group', 'key'], 'uq_app_settings_group_key');

            $table->foreign('updated_by', 'fk_app_settings_updated_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
