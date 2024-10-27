<?php

namespace App\Console\Commands;

use App\Enums\Timezone;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This updates a user\'s firstname, lastname and timezone to new random ones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::whereEmail($this->argument('user'))->first();

        if (is_null($user)) {
            $this->error("User not found with specified email.");
            return;
        }

        // Get the timezones that's not the user's timezone.
        $timezones = array_diff(Timezone::values(), [$user->timezone->value]);

        $user->firstname = fake()->firstName;
        $user->lastname = fake()->lastName;
        $user->timezone = fake()->randomElement($timezones);

        $user->save();

        $this->info('User udpated');
    }
}
