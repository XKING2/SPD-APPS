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
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('unique_id')->unique();
            $table->string('otp'); // HASHED OTP
            $table->enum('type', ['register', 'reset_password']);
            $table->enum('send_via', ['email']);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('resend')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active','valid','expired','blocked'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
