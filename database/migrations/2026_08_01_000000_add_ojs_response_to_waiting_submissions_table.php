<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waiting_submissions', function (Blueprint $table) {
            $table->json('ojs_response')->nullable()->after('ojs_account_created_at');
        });
    }

    public function down(): void
    {
        Schema::table('waiting_submissions', function (Blueprint $table) {
            $table->dropColumn('ojs_response');
        });
    }
};
