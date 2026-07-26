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
        Schema::create('waiting_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_code')->unique();

            // A. Corresponding author account information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('whatsapp_number', 30);
            $table->string('institution');
            $table->string('country');
            $table->string('orcid_id')->nullable();
            $table->string('scopus_or_scholar_url')->nullable();

            // B. Article information
            $table->foreignId('target_journal_id')
                ->constrained('journals')
                ->restrictOnDelete();
            $table->enum('article_type', [
                'research_article',
                'community_service_article',
                'systematic_literature_review',
            ]);
            $table->string('article_language', 50);
            $table->string('article_title');
            $table->longText('abstract');
            $table->json('keywords');
            $table->longText('reference_list');
            $table->string('correspondence_email')->nullable();
            $table->string('funding_source')->nullable();

            // C. Additional information for international authors
            $table->boolean('has_international_authors')->default(false);
            $table->json('international_authors')->nullable();
            $table->boolean('international_author_confirmation')->default(false);

            // D. Author declarations
            $table->boolean('is_original_work')->default(false);
            $table->boolean('not_previously_published')->default(false);
            $table->boolean('not_under_consideration')->default(false);
            $table->boolean('all_authors_approved')->default(false);
            $table->boolean('authorship_information_correct')->default(false);
            $table->boolean('international_authors_agreed')->default(false);
            $table->boolean('uses_official_template')->default(false);
            $table->boolean('agrees_peer_review')->default(false);
            $table->boolean('agrees_publication_process')->default(false);
            $table->boolean('agrees_publication_fees')->default(false);

            $table->enum('status', [
                'waiting',
                'under_review',
                'accepted',
                'rejected',
            ])->default('waiting');
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_submissions');
    }
};
