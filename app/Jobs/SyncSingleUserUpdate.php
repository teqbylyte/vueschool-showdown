<?php

namespace App\Jobs;

use App\Models\User;
use Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class SyncSingleUserUpdate implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Initiate request to update single user.
        $res = Http::post('http://third-party-api.url/update-user', $this->user->for3rdParty());

        if ($res->failed()) {
            Log::error('Single updated failed');
        }
    }
}
