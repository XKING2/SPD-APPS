<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_prak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kop_surat')->nullable();
            $table->string('format_dokumen')->nullable();
            $table->string('layout_ttd')->nullable();
            $table->string('manajemen_file_waktu')->nullable();
            $table->string('format_visualisasi_tabel')->nullable();
            $table->string('fungsi_logika')->nullable();
            $table->string('fungsi_lanjutan')->nullable();
            $table->string('format_data')->nullable();
            $table->string('output_ttd')->nullable();
            $table->string('manajemen_file_excel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prak_result');
    }
};
