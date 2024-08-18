<?php

namespace App\Services;

use App\DTOs\HolidayPlanDTO;
use App\Models\HolidayPlan;

class HolidayPlanService
{
    public function createHolidayPlan(HolidayPlanDTO $holidayPlanDTO, array $participantsIds = []): HolidayPlan
    {
        $holidayPlan = new HolidayPlan();
        $holidayPlan->title = $holidayPlanDTO->title;
        $holidayPlan->description = $holidayPlanDTO->description;
        $holidayPlan->date = $holidayPlanDTO->date;
        $holidayPlan->location = $holidayPlanDTO->location;
        $holidayPlan->owner_id = $holidayPlanDTO->owner->id;
        $holidayPlan->save();

        $holidayPlan->participants()->sync($participantsIds);

        return $holidayPlan;
    }

    public function updateHolidayPlan(HolidayPlanDTO $holidayPlanDTO, HolidayPlan $holidayPlan, array $participantsIds = []): HolidayPlan
    {
        $holidayPlan->title = $holidayPlanDTO->title;
        $holidayPlan->description = $holidayPlanDTO->description;
        $holidayPlan->date = $holidayPlanDTO->date;
        $holidayPlan->location = $holidayPlanDTO->location;
        $holidayPlan->save();

        $holidayPlan->participants()->sync($participantsIds);

        return $holidayPlan;
    }
}
