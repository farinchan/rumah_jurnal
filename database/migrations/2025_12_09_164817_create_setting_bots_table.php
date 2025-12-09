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
        Schema::create('setting_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_production_url');
            $table->string('api_sandbox_url');
            $table->longText('system_message')->nullable();
            $table->longText('additional_context')->nullable();
            $table->text('signature')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_whatsapp_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_bots');
    }
};
