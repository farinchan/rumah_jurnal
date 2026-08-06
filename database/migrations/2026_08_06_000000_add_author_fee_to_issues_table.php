<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('issues', 'author_fee')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->integer('author_fee')->default(0)->after('status');
            });
        }

        // Backfill author_fee for existing issues from parent journal
        $journals = DB::table('journals')->select('id', 'author_fee')->get();
        foreach ($journals as $journal) {
            DB::table('issues')
                ->where('journal_id', $journal->id)
                ->where(function ($query) {
                    $query->whereNull('author_fee')
                        ->orWhere('author_fee', 0);
                })
                ->update(['author_fee' => $journal->author_fee ?? 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('issues', 'author_fee')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('author_fee');
            });
        }
    }
};
