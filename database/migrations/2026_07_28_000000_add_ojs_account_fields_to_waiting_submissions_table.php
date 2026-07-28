<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waiting_submissions', function (Blueprint $table) {
            $table->text('password')->change();
            $table->string('ojs_user_id')->nullable();
            $table->timestamp('ojs_account_created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('waiting_submissions', function (Blueprint $table) {
            $table->string('password')->change();
            $table->dropColumn([
                'ojs_user_id',
                'ojs_account_created_at',
            ]);
        });
    }
};
