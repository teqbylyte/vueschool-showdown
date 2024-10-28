<?php

namespace App\Console\Commands;

use App\Jobs\SyncUsersUpdateInBatch;
use App\Models\ApiCall;
use App\Models\User;
use Illuminate\Console\Command;

class PrepareBatchUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare-batch-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the users to be sent for batch update to the 3rd party provider.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure 3rd party api call for single requests have been exhausted before calling batch updates
        if (! ApiCall::isWithinSingleLimit()) {
            // Get the users since the last single api call request but within the current minute.
            User::where('updated_at', '>', ApiCall::single()->value('updated_at'))
                ->where('updated_at', '>=', now()->subMinute())
                ->chunk(1000, function($users) {
                    SyncUsersUpdateInBatch::dispatch($users);
                });
        }
    }
}
