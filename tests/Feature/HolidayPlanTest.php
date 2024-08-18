<?php

namespace Tests\Feature;

use App\DTOs\HolidayPlanDTO;
use App\Models\HolidayPlan;
use App\Models\User;
use App\Services\HolidayPlanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class HolidayPlanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test store method of HolidayPlanService.
     *
     * @return void
     */
    public function test_store_holiday_plan_service()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);

        $this->assertDatabaseHas('holiday_plans', [
            'title' => 'Christmas party',
            'description' => 'Christmas party description',
            'location' => 'London',
            'owner_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('participants', [
            'user_id' => $participant1->id,
            'holiday_plan_id' => $holidayPlan->id,
        ]);

        $this->assertDatabaseHas('participants', [
            'user_id' => $participant2->id,
            'holiday_plan_id' => $holidayPlan->id,
        ]);

        $this->assertEquals($holidayPlan->owner->id, $owner->id);
        $this->assertEquals(2, $holidayPlan->participants->count());
    }

    /**
     * Test update method of HolidayPlanService.
     *
     * @return void
     */
    public function test_update_holiday_plan_service()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);

        $holidayPlanDTO = new HolidayPlanDTO(
            'New Year party',
            'New Year party description',
            Carbon::now(),
            'Paris',
            $owner
        );

        $holidayPlan = $holidayPlanService->updateHolidayPlan($holidayPlanDTO, $holidayPlan, [$participant1->id]);

        $this->assertDatabaseHas('holiday_plans', [
            'title' => 'New Year party',
            'description' => 'New Year party description',
            'location' => 'Paris',
            'owner_id' => $owner->id,
        ]);

        $this->assertDatabaseHas('participants', [
            'user_id' => $participant1->id,
            'holiday_plan_id' => $holidayPlan->id,
        ]);

        $this->assertDatabaseMissing('participants', [
            'user_id' => $participant2->id,
            'holiday_plan_id' => $holidayPlan->id,
        ]);

        $this->assertEquals($holidayPlan->owner->id, $owner->id);
        $this->assertEquals(1, $holidayPlan->participants->count());
    }

    /**
     * Test list holiday plans route.
     *
     * @return void
     */
    public function test_list_holiday_plans()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);
        HolidayPlan::factory()->count(5)->create([
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($owner);
        $response = $this->getJson('/api/holiday-plans');

        $response->assertStatus(200);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->has('holiday_plans', 6)
            ->has('holiday_plans.0', fn(AssertableJson $json) => $json
                ->where('id', $holidayPlan->id)
                ->where('title', 'Christmas party')
                ->where('description', 'Christmas party description')
                ->where('location', 'London')
                ->where('date', $holidayPlan->date->format('Y-m-d'))
                ->has('owner', fn(AssertableJson $json) => $json
                    ->where('id', $owner->id)
                    ->where('name', $owner->name)
                    ->where('email', $owner->email)
                )
                ->has('participants', 2)
                ->has('participants.0', fn(AssertableJson $json) => $json
                    ->where('id', $participant1->id)
                    ->where('name', $participant1->name)
                    ->where('email', $participant1->email)
                )
                ->has('participants.1', fn(AssertableJson $json) => $json
                    ->where('id', $participant2->id)
                    ->where('name', $participant2->name)
                    ->where('email', $participant2->email)
                )
            )
            ->etc()
        );
    }

    /**
     * Test show holiday plan route.
     *
     * @return void
     */
    public function test_show_holiday_plan()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);

        $this->actingAs($owner);
        $response = $this->getJson("/api/holiday-plans/{$holidayPlan->id}");

        $response->assertStatus(200);
        $response->assertExactJson([
            'holiday_plan' => [
                'id' => $holidayPlan->id,
                'title' => 'Christmas party',
                'description' => 'Christmas party description',
                'location' => 'London',
                'date' => $holidayPlan->date->format('Y-m-d'),
                'owner' => [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                ],
                'participants' => [
                    [
                        'id' => $participant1->id,
                        'name' => $participant1->name,
                        'email' => $participant1->email,
                    ],
                    [
                        'id' => $participant2->id,
                        'name' => $participant2->name,
                        'email' => $participant2->email,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test store holiday plan route.
     *
     * @return void
     */
    public function test_store_holiday_plan()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $this->actingAs($owner);
        $response = $this->postJson('/api/holiday-plans', [
            'title' => 'Christmas party',
            'description' => 'Christmas party description',
            'date' => Carbon::now()->format('Y-m-d'),
            'location' => 'London',
            'participants' => [$participant1->id, $participant2->id],
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'holiday_plan' => [
                'id',
                'title',
                'description',
                'date',
                'location',
                'owner',
                'participants',
            ],
        ]);
    }

    /**
     * Test update holiday plan route.
     *
     * @return void
     */
    public function test_update_holiday_plan()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);

        $this->actingAs($owner);
        $response = $this->putJson("/api/holiday-plans/{$holidayPlan->id}", [
            'title' => 'New Year party',
            'description' => 'New Year party description',
            'date' => Carbon::now()->addDay()->format('Y-m-d'),
            'location' => 'Paris',
            'participants' => [$participant1->id],
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'holiday_plan' => [
                'id' => $holidayPlan->id,
                'title' => 'New Year party',
                'description' => 'New Year party description',
                'location' => 'Paris',
                'date' => Carbon::now()->addDay()->format('Y-m-d'),
                'owner' => [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                ],
                'participants' => [
                    [
                        'id' => $participant1->id,
                        'name' => $participant1->name,
                        'email' => $participant1->email,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test delete holiday plan route.
     *
     * @return void
     */
    public function test_delete_holiday_plan()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);

        $this->actingAs($owner);
        $response = $this->deleteJson("/api/holiday-plans/{$holidayPlan->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('holiday_plans', [
            'id' => $holidayPlan->id,
        ]);
    }

    /**
     * Test view holiday plan route with unauthorized user.
     *
     * @return void
     */
    public function test_view_holiday_plan_unauthorized()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $participant */
        $participant = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant->id]);

        $this->actingAs($user);
        $response = $this->getJson("/api/holiday-plans/{$holidayPlan->id}");

        $response->assertStatus(403);
    }

    /**
     * Test update holiday plan route with unauthorized user.
     *
     * @return void
     */
    public function test_update_holiday_plan_unauthorized()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $participant */
        $participant = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant->id]);

        $this->actingAs($user);
        $response = $this->putJson("/api/holiday-plans/{$holidayPlan->id}", [
            'title' => 'New Year party',
            'description' => 'New Year party description',
            'date' => Carbon::now()->addDay()->format('Y-m-d'),
            'location' => 'Paris',
            'participants' => [$participant->id],
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test delete holiday plan route with unauthorized user.
     *
     * @return void
     */
    public function test_delete_holiday_plan_unauthorized()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $participant */
        $participant = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant->id]);

        $this->actingAs($user);
        $response = $this->deleteJson("/api/holiday-plans/{$holidayPlan->id}");

        $response->assertStatus(403);
    }

    public function test_export_pdf()
    {
        /** @var User $owner */
        $owner = User::factory()->create();
        /** @var User $participant1 */
        $participant1 = User::factory()->create();
        /** @var User $participant2 */
        $participant2 = User::factory()->create();

        $holidayPlanDTO = new HolidayPlanDTO(
            'Christmas party',
            'Christmas party description',
            Carbon::now(),
            'London',
            $owner
        );

        $holidayPlanService = new HolidayPlanService();
        $holidayPlan = $holidayPlanService->createHolidayPlan($holidayPlanDTO, [$participant1->id, $participant2->id]);

        $this->actingAs($owner);
        $response = $this->get("/api/holiday-plans/{$holidayPlan->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
