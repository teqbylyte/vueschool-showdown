<?php

use App\Jobs\SyncUsersUpdateInBatch;
use App\Models\User;

test('that the user can be formatted to 3rd party api structure', function() {
    $user = User::factory()->create();

    $data = $user->for3rdParty();

    expect($data)->toBeArray()
        ->toHaveKeys(['email', 'name', 'time_zone']);
});

test('that batch requests body contains the right structure', function () {
    User::factory(20)->create();

    $payload = (new SyncUsersUpdateInBatch(User::all()))->payload();

    expect($payload)->toBeArray();
    expect($payload['batches'])->toBeArray();
    expect($payload['batches'][0])->toHaveKey('subscribers');
    expect($payload['batches'][0]['subscribers'])->toBeArray();
    expect($payload['batches'][0]['subscribers'][0])->toHaveKey('email');
});
