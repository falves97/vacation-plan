<?php

namespace App\DTOs;

use App\Models\User;
use Carbon\Carbon;

final readonly class HolidayPlanDTO
{
    public string $title;
    public string $description;
    public Carbon $date;
    public string $location;
    public User $owner;

    public function __construct(string $title, string $description, Carbon $date, string $location, User $owner)
    {
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->location = $location;
        $this->owner = $owner;
    }
}
