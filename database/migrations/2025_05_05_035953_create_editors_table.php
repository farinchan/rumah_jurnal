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
        Schema::create('editors', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('editor_id')->nullable();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('affiliation')->nullable();
            $table->json('groups')->nullable();
            $table->string('account_bank')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editors');
    }
};
