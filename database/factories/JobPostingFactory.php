<?php

namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => JobPosting::uniqueSlug($title),
            'description' => fake()->paragraphs(3, true),
            'company' => fake()->company(),
            'location' => fake()->city(),
            'type' => fake()->randomElement(JobPosting::TYPES),
            'work_mode' => fake()->randomElement(JobPosting::WORK_MODES),
            'salary_min' => fake()->numberBetween(30, 80) * 1000,
            'salary_max' => fake()->numberBetween(81, 150) * 1000,
            'salary_currency' => 'USD',
            'expires_at' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'is_open' => true,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subWeek(),
        ]);
    }

    public function remote(): static
    {
        return $this->state(fn (array $attributes) => [
            'work_mode' => 'remote',
        ]);
    }

    public function noSalary(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_min' => null,
            'salary_max' => null,
        ]);
    }
}
