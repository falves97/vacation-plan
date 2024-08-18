<?php

namespace App\Reposiories;

use App\Models\HolidayPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HolidayPlanRepository
{
    /**
     * Lista todos os planos de férias de um usuário, incluindo os planos em que ele é participante.
     *
     * @param int $ownerId
     * @return LengthAwarePaginator
     */
    public function findAllByOwner(int $ownerId): LengthAwarePaginator
    {
        return HolidayPlan::query()
            ->where('owner_id', $ownerId)
            ->orWhereHas('participants', function ($query) use ($ownerId) {
                $query->where('user_id', $ownerId);
            })
            ->paginate();
    }
}
