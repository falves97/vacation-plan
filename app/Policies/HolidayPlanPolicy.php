<?php

namespace App\Policies;

use App\Models\HolidayPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HolidayPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HolidayPlan $holidayPlan): Response
    {
        if ($user->id === $holidayPlan->owner_id || $holidayPlan->participants->contains($user)) {
            return Response::allow();
        }

        return Response::denyWithStatus(403);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HolidayPlan $holidayPlan): Response
    {
        if ($user->id === $holidayPlan->owner_id) {
            return Response::allow();
        }

        return Response::denyWithStatus(403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HolidayPlan $holidayPlan): Response
    {
        if ($user->id === $holidayPlan->owner_id) {
            return Response::allow();
        }

        return Response::denyWithStatus(403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HolidayPlan $holidayPlan): Response
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HolidayPlan $holidayPlan): Response
    {
        //
    }
}
