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
    protected $signature = 'app:prepare-batch-update';

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
        // Ensure 3rd party api call for single requests have been exhausted.
        if (! ApiCall::isWithinSingleLimit()) {

            // Get the users since the last single api call request and within the current minute.
            User::whereDate('updated_at', '>', ApiCall::single()->value('updated_at'))
                ->whereDate('updated_at', '<=', now()->addMinute())
                ->chunk(1000, function($users) {
                    SyncUsersUpdateInBatch::dispatch($users);
                });
        }
    }
}
