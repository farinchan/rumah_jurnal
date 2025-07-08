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
        Schema::create('event_attendance_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_attendance_id')->constrained()->onDelete('cascade');
            $table->uuid('event_user_id');
            $table->foreign('event_user_id')->references('id')->on('event_users')->onDelete('cascade');
            $table->dateTime('attendance_datetime')->nullable();
            $table->text('notes')->nullable(); // Additional notes for attendance
            $table->string('ip_address')->nullable(); // IP address of the user when marking attendance
            $table->string('user_agent')->nullable(); // User agent string for device information
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendance_users');
    }
};
