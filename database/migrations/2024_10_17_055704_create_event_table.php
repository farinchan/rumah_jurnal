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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->enum('access', ['terbuka', 'tertutup'])->default('terbuka');
            $table->string('name');
            $table->string('slug');
            $table->string('datetime')->nullable();
            $table->string('location')->nullable();
            $table->integer('limit')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('material')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
