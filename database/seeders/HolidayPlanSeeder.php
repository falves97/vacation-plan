<?php

namespace Database\Seeders;

use App\Models\HolidayPlan;
use Illuminate\Database\Seeder;

class HolidayPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HolidayPlan::query()->truncate();

        HolidayPlan::factory()->count(20)->create([
            'owner_id' => 1,
        ]);
    }
}
