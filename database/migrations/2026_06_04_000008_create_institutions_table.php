<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            // ✅ Enum final: pemerintah, swasta, bumn, pendidikan, lainnya
            $table->enum('type', ['pemerintah', 'swasta', 'bumn', 'pendidikan', 'lainnya'])
                  ->default('swasta');
            $table->string('sector', 255)->nullable(); // bidang usaha
            $table->string('website', 255)->nullable();
            $table->string('logo', 255)->nullable();   // ✅ path logo (ganti logo_path lama)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['type', 'is_active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};