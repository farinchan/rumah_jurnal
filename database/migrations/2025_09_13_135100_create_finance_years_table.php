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
        Schema::create('finance_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('distribution_percentage')->default(80)->comment('persentase yang didapatkan untuk rumah jurnal');
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable();
            $table->enum('type_control', ['journal', 'proceeding', 'student_research_hub']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_years');
    }
};
