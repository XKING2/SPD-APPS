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
        Schema::table('biodata', function (Blueprint $table) {
            $table->foreignId('id_formasi')->nullable()->after('id_user')->constrained('formasis')->nullOnDelete();
            $table->foreignId('id_kebutuhan')->nullable()->after('id_formasi')->constrained('kebutuhan_formasi')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biodata', function (Blueprint $table) {
            //
        });
    }
};
