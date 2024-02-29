<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'archived_at' => fake()->boolean() ? fake()->dateTimeBetween('-6 months', '-1 day') : null,
            'due_date' => fake()->boolean() ? fake()->dateTimeBetween('-6 months', '+6 months') : null,
        ];
    }

    /**
     * Indicate that the task status is todo.
     *
     * @return TaskFactory
     */
    public function todo(): TaskFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'status' => TaskStatus::Todo,
            ];
        });
    }

    /**
     * Indicate that the task status is in progress.
     *
     * @return TaskFactory
     */
    public function inProgress(): TaskFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'status' => TaskStatus::InProgress,
            ];
        });
    }

    /**
     * Indicate that the task status is completed.
     *
     * @return TaskFactory
     */
    public function completed(): TaskFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'status' => TaskStatus::Completed,
            ];
        });
    }

    /**
     * Indicate that the task is not archived.
     *
     * @return TaskFactory
     */
    public function active(): TaskFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'archived_at' => null,
            ];
        });
    }
}
