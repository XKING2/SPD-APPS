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
        Schema::create('biodata', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->foreignId('id_user')->nullable()->constrained('users') ->cascadeOnDelete();
            $table->string('kartu_keluarga')->nullable();
            $table->string('ktp')->nullable();
            $table->string('ijazah')->nullable();
            $table->string('cv')->nullable();
            $table->string('surat_pendaftaran')->nullable();
            $table->string('profile_img')->nullable();
            $table->enum('status', ['valid','draft','ditolak'])->default('draft');
            $table->boolean('notified')->default(false);
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata');
    }
};
