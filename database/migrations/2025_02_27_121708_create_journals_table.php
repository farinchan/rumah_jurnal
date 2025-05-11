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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('context_id');
            $table->string('url');
            $table->string('url_path')->unique();
            $table->string('title');
            $table->mediumText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('onlineIssn')->nullable();
            $table->string('printIssn')->nullable();
            $table->integer('author_fee')->default(0);
            $table->json('indexing')->nullable();
            $table->json('indexing_others')->nullable();
            $table->string('api_key');
            $table->enum('ojs_version', ['3.3', '3.4']);
            $table->dateTime('last_sync');
            $table->string('editor_chief_name')->nullable();
            $table->string('editor_chief_signature')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
