<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;

class DeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = search(
            label: 'Which user do you want to delete?',
            options: fn (string $value) => !empty($value)
                ? User::search($value)->get()->pluck('name', 'id')->toArray()
                : User::pluck('name', 'id')->toArray(),
			scroll: 10,
        );

		$user = User::find($userId);

		if(confirm("Are you sure you want to delete {$user->name}?")) {
			$user->delete();

			info("User {$user->name} has been deleted.");

			return;
		}

		error("User {$user->name} was not deleted.");
    }
}
