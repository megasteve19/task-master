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
            'is_archived' => fake()->boolean(),
            'due_date' => fake()->boolean() ? fake()->dateTimeBetween('-6 months', '+6 months') : null,
        ];
    }

    /**
     * Indicate that the task is archived.
     *
     * @return TaskFactory
     */
    public function archived(): TaskFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'is_archived' => true,
            ];
        });
    }

    /**
     * Indicate that the task is not archived.
     *
     * @return TaskFactory
     */
    public function notArchived(): TaskFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'is_archived' => false,
            ];
        });
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
}
