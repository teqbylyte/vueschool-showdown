<?php

use App\Models\ApiCall;
use Illuminate\Support\Facades\Artisan;

Artisan::command('reset-api-calls', function () {
    DB::table('api_calls')->update(['count' => 0]);
})->purpose('Reset the values of the api-calls so that the update process can begin normally.')->hourly();

// Throttle the queue worker based on the batch ApiCall limit
if (ApiCall::isWithinBatchLimit()) {
    // Run queue worker every minute
}

Schedule::command('prepare-batch-update')->everyMinute();
