<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answer_types', function (Blueprint $table) {
            // PRIMARY KEY — UUID CHAR(36), konsisten dengan seluruh Phase 1-3
            $table->uuid('id')->primary();

            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->json('config')->nullable();  // {min, max, labels, options}
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            // Tidak ada softDeletes — answer_types adalah data master referensi

            // Audit fields — FK ke users.id (UUID CHAR(36))
            // Tidak ada deleted_by karena tidak ada softDeletes
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_atype_created_by');

            $table->foreign('updated_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->name('fk_atype_updated_by');

            $table->index('code', 'idx_atype_code');
            $table->index('is_active', 'idx_atype_is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_types');
    }
};
