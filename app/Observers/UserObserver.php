<?php

namespace App\Observers;

use App\Jobs\SyncSingleUserUpdate;
use App\Models\ApiCall;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged(['firstname', 'lastname', 'timezone'])) {
            if (ApiCall::isWithinSingleLimit()) {
                // Increase the value of the count.
                ApiCall::single()->increment('count');

                SyncSingleUserUpdate::dispatch($user);
            }
        }
    }
}
