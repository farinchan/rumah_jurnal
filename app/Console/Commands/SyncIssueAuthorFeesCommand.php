<?php

namespace App\Console\Commands;

use App\Models\Issue;
use Illuminate\Console\Command;

class SyncIssueAuthorFeesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issues:sync-author-fee {--force : Force overwrite existing author_fee for all issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy author_fee from parent Journal to existing Issue records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting issue author_fee synchronization...');

        $force = $this->option('force');

        $query = Issue::with('journal');
        if (! $force) {
            $query->where(function ($q) {
                $q->whereNull('author_fee')
                  ->orWhere('author_fee', 0);
            });
        }

        $issues = $query->get();

        if ($issues->isEmpty()) {
            $this->info('No issues found requiring author_fee synchronization.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($issues->count());
        $bar->start();

        $updatedCount = 0;

        foreach ($issues as $issue) {
            $journalFee = $issue->journal?->author_fee ?? 0;

            if ($force || is_null($issue->author_fee) || (int) $issue->author_fee === 0) {
                $issue->update([
                    'author_fee' => $journalFee,
                ]);
                $updatedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully synchronized author_fee for {$updatedCount} issue(s).");

        return self::SUCCESS;
    }
}
