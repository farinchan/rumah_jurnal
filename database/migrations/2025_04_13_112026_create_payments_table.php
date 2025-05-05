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
            $table->timestamp('payment_timestamp')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_account_number')->nullable();
            $table->string('payment_account_name')->nullable();
            $table->string('payment_file')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('payment_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('payment_note')->nullable();
            $table->foreignId('payment_invoice_id')->nullable()->constrained('payment_invoices')->onDelete('cascade');
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
