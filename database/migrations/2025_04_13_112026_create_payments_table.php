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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->timestamp('payment_timestamp')->nullable();
            $table->string('payment_method')->nullable();
            $table->integer('payment_amount')->nullable();
            $table->string('payment_account_number')->nullable();
            $table->string('payment_account_name')->nullable();
            $table->string('payment_file')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('payment_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('payment_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
