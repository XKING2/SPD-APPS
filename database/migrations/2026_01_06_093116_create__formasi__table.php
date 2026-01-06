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
        Schema::create('formasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_seleksi')->nullable()->constrained('selections') ->cascadeOnDelete();
            $table->foreignId('id_desas')->nullable()->constrained('desas')->nullOnDelete();
            $table->string('tahun')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formasis');
    }
};
