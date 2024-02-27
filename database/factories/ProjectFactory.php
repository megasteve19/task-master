<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->realText(),
            'archived_at' => fake()->boolean() ? fake()->dateTimeBetween('-6 months', '-1 day') : null,
            'due_date' => fake()->boolean() ? fake()->dateTimeBetween('-6 months', '+6 months') : null,
        ];
    }

    /**
     * Indicate that the project is due today.
     *
     * @return ProjectFactory
     */
    public function dueToday(): ProjectFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'due_date' => now(),
            ];
        });
    }

    /**
     * Indicate that the project is due tomorrow.
     *
     * @return ProjectFactory
     */
    public function dueTomorrow(): ProjectFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'due_date' => now()->addDay(),
            ];
        });
    }

    /**
     * Indicate that the project is due in 7 days.
     *
     * @return ProjectFactory
     */
    public function dueInAWeek(): ProjectFactory
    {
        return $this->state(function(array $attributes) {
            return [
                'due_date' => now()->addWeek(),
            ];
        });
    }
}
