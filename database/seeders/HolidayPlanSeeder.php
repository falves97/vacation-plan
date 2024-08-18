<?php

namespace Database\Seeders;

use App\Models\HolidayPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class HolidayPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HolidayPlan::query()->truncate();

        for ($i = 0; $i < 30; $i++) {
            $owner = User::inRandomOrder()->first();

            HolidayPlan::factory()->create([
                'owner_id' => $owner->id,
            ]);
        }
    }
}
