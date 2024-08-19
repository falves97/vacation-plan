<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property Carbon $date
 * @property string $location
 * @property int $owner_id
 * @property User $owner
 * @property Collection<User> $participants
 */
class HolidayPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'owner_id',
    ];

    public function date(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value),
            set: fn ($value) => $value->format('Y-m-d')
        );
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants', 'holiday_plan_id', 'user_id');
    }
}
