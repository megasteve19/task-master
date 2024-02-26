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
            'name' => 'Abdulkadir Cemiloğlu',
            'email' => 'kadir.cemiloglu1@gmail.com',
        ])
            ->owner()
            ->has(
                Task::factory()
                    ->count(3)
            )
            ->has(
                Project::factory()
                    ->has(
                        Task::factory()
                            ->count(3)
                    )
            )
            ->create();
    }
}
