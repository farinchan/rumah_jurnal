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
            $table->integer('context_id');
            $table->string('url');
            $table->string('url_path');
            $table->string('title');
            $table->mediumText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('onlineIssn')->nullable();
            $table->string('printIssn')->nullable();
            $table->integer('author_fee')->nullable();
            $table->json('indexing')->nullable();
            $table->string('api_key');
            $table->dateTime('last_sync');
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
