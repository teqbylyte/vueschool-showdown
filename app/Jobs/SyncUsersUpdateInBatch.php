<?php

namespace App\Jobs;

use App\Models\ApiCall;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Log;

class SyncUsersUpdateInBatch implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(public Collection $users)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $suscribers = $this->users->map(fn(User $user) => $user->for3rdParty());

        $payload = [
            'batches' => [
                ['suscribers' => $suscribers->toArray()]
            ]
        ];

        ApiCall::batch()->increment('count');

        // Make batch api call
        $res = Http::post('http://third-party-api.url/batch-update', $payload);

        if ($res->failed()) {
            Log::error('Batch update failed');
        }
    }
}
