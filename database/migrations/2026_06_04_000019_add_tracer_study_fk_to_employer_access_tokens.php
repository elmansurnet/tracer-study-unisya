<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan FK tracer_study_id ke employer_access_tokens setelah
     * tracer_studies sudah ada. Dipisah agar urutan migration aman.
     */
    public function up(): void
    {
        Schema::table('employer_access_tokens', function (Blueprint $table) {
            $table->foreign('tracer_study_id')
                  ->references('id')->on('tracer_studies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employer_access_tokens', function (Blueprint $table) {
            $table->dropForeign(['tracer_study_id']);
        });
    }
};