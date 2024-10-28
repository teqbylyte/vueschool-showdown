<?php

use App\Enums\Timezone;
use App\Jobs\SyncSingleUserUpdate;
use App\Jobs\SyncUsersUpdateInBatch;
use App\Models\ApiCall;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

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

test('that api call is incremented when a single user is updated', function() {
    Queue::fake();

    $user = User::factory()->create();

    // Set api count to 0
    $api_call = ApiCall::single()->first();
    $api_call->count = 0;
    $api_call->save();

    $timezone = fake()->randomElement(array_diff(Timezone::values(), [$user->timezone->value]));


    $user->timezone = $timezone;
    $user->save();

    expect($user->timezone->value)->toBe($timezone);
    expect(ApiCall::single()->value('count'))->toBe(++$api_call->count);
    Queue::assertPushed(SyncSingleUserUpdate::class);
});

test('that job is not dispatched for single user when api call reaches 3600', function() {
    Queue::fake();

    $user = User::factory()->create();
    $api_call = ApiCall::single()->first();
    $api_call->count = 3600;
    $api_call->save();
    $name = fake()->firstName;

    $user->firstname = $name;
    $user->save();

    expect($user->firstname)->toBe($name);
    expect(ApiCall::single()->value('count'))->toBe(3600);
    Queue::assertNotPushed(SyncSingleUserUpdate::class);
});

test('that batch job is dispatched when single calls reach limit', function() {
    Queue::fake();

    // Set to max single limit
    $api_call = ApiCall::single()->first();
    $api_call->count = 3600;
    $api_call->updated_at = now()->subMinute();
    $api_call->save();

    User::factory(3000)->create();

    // Act: run the command
    $this->artisan('prepare-batch-update')
        ->assertExitCode(0);

    Queue::assertPushed(SyncUsersUpdateInBatch::class);
});

test('that api call for batch request is incremented when batch job runs.', function() {
    Http::fake([
        'http://third-party-api.url/batch-update' => Http::response(['success' => true], 200),
    ]);

    $count = ApiCall::batch()->value('count');

    SyncUsersUpdateInBatch::dispatch(User::limit(1000)->get());

    expect(ApiCall::batch()->value('count'))->toBe(++$count);
});
