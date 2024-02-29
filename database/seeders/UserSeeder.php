<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory([
            'name' => 'Owner',
            'email' => 'owner@example.com',
        ])
            ->owner()
            ->has(
                Task::factory()
                    ->count(3)
            )
            ->has(
                Project::factory()
                    ->active()
                    ->has(
                        Task::factory()
							->active()
                            ->count(3)
                    )
            )
            ->create();
    }
}
