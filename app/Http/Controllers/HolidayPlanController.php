<?php

namespace App\Http\Controllers;

use App\DTOs\HolidayPlanDTO;
use App\Http\Requests\StoreHolidayPlanRequest;
use App\Http\Requests\UpdateHolidayPlanRequest;
use App\Http\Resources\HolidayPlanCollection;
use App\Http\Resources\HolidayPlanResource;
use App\Models\HolidayPlan;
use App\Models\User;
use App\Reposiories\HolidayPlanRepository;
use App\Services\HolidayPlanService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class HolidayPlanController
{
    /**
     * Display a listing of the resource.
     *
     * @param HolidayPlanRepository $holidayPlanRepository
     * @return JsonResponse
     */
    public function index(HolidayPlanRepository $holidayPlanRepository): JsonResponse
    {
        $holidayPlans = $holidayPlanRepository->findAllByOwner(auth()->id());

        return (new HolidayPlanCollection($holidayPlans))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreHolidayPlanRequest $request
     * @param HolidayPlanService $holidayPlanService
     * @return JsonResponse
     */
    public function store(StoreHolidayPlanRequest $request, HolidayPlanService $holidayPlanService): JsonResponse
    {
        $validated = $request->validated();
        /** @var User $owner */
        $owner = auth()->user();

        $holidayPlanDTO = new HolidayPlanDTO(
            $validated['title'],
            $validated['description'],
            Carbon::createFromFormat('Y-m-d', $validated['date']),
            $validated['location'],
            $owner
        );

        $participantsIds = $validated['participants'] ?? [];

        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, $participantsIds);

        return (new HolidayPlanResource($holidayPlan))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param HolidayPlan $holidayPlan
     * @return JsonResponse
     */
    public function show(HolidayPlan $holidayPlan): JsonResponse
    {
        Gate::authorize('view', $holidayPlan);
        return (new HolidayPlanResource($holidayPlan))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHolidayPlanRequest $request
     * @param HolidayPlan $holidayPlan
     * @param HolidayPlanService $holidayPlanService
     * @return JsonResponse
     */
    public function update(UpdateHolidayPlanRequest $request, HolidayPlan $holidayPlan, HolidayPlanService $holidayPlanService): JsonResponse
    {
        $validated = $request->validated();

        Gate::authorize('update', $holidayPlan);

        $holidayPlanDTO = new HolidayPlanDTO(
            $validated['title'] ?? $holidayPlan->title,
            $validated['description'] ?? $holidayPlan->description,
            $validated['date'] ? Carbon::createFromFormat('Y-m-d', $validated['date']) : $holidayPlan->date,
            $validated['location'] ?? $holidayPlan->location,
            $holidayPlan->owner
        );

        $participantsIds = $validated['participants'] ?? [];

        $holidayPlan = $holidayPlanService->updateHolidayPlan($holidayPlanDTO, $holidayPlan, $participantsIds);

        return (new HolidayPlanResource($holidayPlan))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param HolidayPlan $holidayPlan
     * @return JsonResponse
     */
    public function destroy(HolidayPlan $holidayPlan): JsonResponse
    {
        Gate::authorize('delete', $holidayPlan);

        $holidayPlan->delete();

        return response()->json(null, 204);
    }
}
