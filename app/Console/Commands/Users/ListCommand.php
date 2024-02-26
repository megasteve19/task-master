<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::withCount('projects', 'tasks')->get();

        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Projects', 'Tasks'],
            $users->map(function(User $user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role->prettyName(),
                    $user->projects_count,
                    $user->tasks_count,
                ];
            })
        );
    }
}
