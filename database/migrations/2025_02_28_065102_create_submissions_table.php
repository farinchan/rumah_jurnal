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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('submission_id');
            $table->string('publication_id')->nullable();
            $table->string('number')->nullable();
            $table->string('locale')->nullable();

            $table->json('authors')->nullable();
            $table->string('authorsString')->nullable();
            $table->json('fullTitle')->nullable();
            $table->json('abstract')->nullable();
            $table->json('keywords')->nullable();
            $table->json('citations')->nullable();
            $table->string('urlPublished')->nullable();
            $table->string('datePublished')->nullable();
            $table->string('status')->nullable();
            $table->string('status_label')->nullable();
            $table->string('lastModified');
            $table->foreignId('issue_id')->constrained()->onDelete('cascade')->onUpdate('cascade');

            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'cancelled'])->default('pending');
            $table->boolean('free_billing')->default(false);
            $table->string('invoice_number')->unique()->nullable();
            $table->string('invoice_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
