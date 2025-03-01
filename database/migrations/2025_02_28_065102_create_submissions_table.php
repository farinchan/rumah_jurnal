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
            $table->string('authors')->nullable();
            $table->string('publication_id')->nullable();
            $table->string('publication_author_email')->nullable();
            $table->string('publication_author_affiliation')->nullable();
            $table->string('publication_author_id')->nullable();
            $table->string('publication_author_orcid')->nullable();
            $table->string('publication_title')->nullable();
            $table->string('publication_abstract')->nullable();
            $table->json('publication_keywords')->nullable();
            $table->json('publication_citations')->nullable();
            $table->string('status')->nullable();
            $table->string('status_label')->nullable();
            $table->string('lastModified');
            $table->foreignId('issue_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
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
