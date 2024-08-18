<?php

namespace Database\Factories;

use App\Models\HolidayPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HolidayPlan>
 */
class HolidayPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
            'location' => $this->faker->address(),
            'owner_id' => User::factory(),
        ];
    }
}
