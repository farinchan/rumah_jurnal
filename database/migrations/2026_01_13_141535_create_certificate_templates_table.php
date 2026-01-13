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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Template name
            $table->foreignId('event_id')->nullable()->constrained('event')->onDelete('cascade');
            $table->string('template_image'); // Path to template image
            $table->string('orientation')->default('landscape'); // landscape or portrait
            $table->string('paper_size')->default('A4'); // A4, Letter, etc.
            $table->json('text_elements')->nullable(); // JSON array of text elements with positions
            $table->string('signature_image')->nullable(); // Path to signature image
            $table->json('signature_position')->nullable(); // Position of signature
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
