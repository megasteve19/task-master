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
            'is_archived' => fake()->boolean(),
            'due_date' => fake()->boolean() ? fake()->dateTimeBetween('-6 months', '+6 months') : null,
        ];
    }

    /**
     * Indicate that the project is archived.
     *
     * @return \Database\Factories\ProjectFactory
     */
    public function archived(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_archived' => true,
            ];
        });
    }

    /**
     * Indicate that the project is not archived.
     *
     * @return \Database\Factories\ProjectFactory
     */
    public function notArchived(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_archived' => false,
            ];
        });
    }

    /**
     * Indicate that the project is due today.
     *
     * @return \Database\Factories\ProjectFactory
     */
    public function dueToday(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => now(),
            ];
        });
    }

    /**
     * Indicate that the project is due tomorrow.
     *
     * @return \Database\Factories\ProjectFactory
     */
    public function dueTomorrow(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => now()->addDay(),
            ];
        });
    }

    /**
     * Indicate that the project is due in 7 days.
     *
     * @return \Database\Factories\ProjectFactory
     */
    public function dueInAWeek(): ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => now()->addWeek(),
            ];
        });
    }
}
