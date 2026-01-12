<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\CalendarAction;
use App\Models\CalendarType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CalendarType $calendarType;
    protected CalendarAction $calendarAction;
    protected Entity $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->calendarType = CalendarType::factory()->create();
        $this->calendarAction = CalendarAction::factory()->create();
        $this->entity = Entity::factory()->create();
    }

    /** @test */
    public function it_displays_calendar_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('calendar.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_calendar_event()
    {
        $eventData = [
            'title' => 'Test Meeting',
            'description' => 'Test description',
            'start_date' => now()->toDateTimeString(),
            'end_date' => now()->addHour()->toDateTimeString(),
            'all_day' => false,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
            'entity_id' => $this->entity->id,
            'user_id' => $this->user->id,
            'color' => '#3788d8',
            'notes' => 'Test notes',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('calendar.store'), $eventData);

        $response->assertRedirect();

        $this->assertDatabaseHas('calendars', [
            'title' => 'Test Meeting',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('calendar.store'), []);

        $response->assertSessionHasErrors([
            'title',
            'start_date',
            'end_date',
            'calendar_type_id',
            'calendar_action_id',
        ]);
    }

    /** @test */
    public function it_can_update_a_calendar_event()
    {
        $event = Calendar::factory()->create([
            'user_id' => $this->user->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
            'title' => 'Old Title',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('calendar.update', $event), [
                'title' => 'New Title',
                'start_date' => now()->toDateTimeString(),
                'end_date' => now()->addHour()->toDateTimeString(),
                'all_day' => false,
                'calendar_type_id' => $this->calendarType->id,
                'calendar_action_id' => $this->calendarAction->id,
                'user_id' => $this->user->id,
                'color' => '#3788d8',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('calendars', [
            'id' => $event->id,
            'title' => 'New Title',
        ]);
    }

    /** @test */
    public function it_can_delete_a_calendar_event()
    {
        $event = Calendar::factory()->create([
            'user_id' => $this->user->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('calendar.destroy', $event));

        $response->assertRedirect();

        $this->assertDatabaseMissing('calendars', [
            'id' => $event->id,
        ]);
    }

    /** @test */
    public function it_filters_events_by_user()
    {
        $user2 = User::factory()->create();

        Calendar::factory()->create([
            'user_id' => $this->user->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        Calendar::factory()->create([
            'user_id' => $user2->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('calendar.index', ['user_id' => $this->user->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_filters_events_by_type()
    {
        $type2 = CalendarType::factory()->create();

        Calendar::factory()->create([
            'user_id' => $this->user->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        Calendar::factory()->create([
            'user_id' => $this->user->id,
            'calendar_type_id' => $type2->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('calendar.index', ['type_id' => $this->calendarType->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_scopes_events_for_user()
    {
        $user2 = User::factory()->create();

        Calendar::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        Calendar::factory()->count(2)->create([
            'user_id' => $user2->id,
            'calendar_type_id' => $this->calendarType->id,
            'calendar_action_id' => $this->calendarAction->id,
        ]);

        $userEvents = Calendar::forUser($this->user->id)->get();

        $this->assertCount(3, $userEvents);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_calendar()
    {
        $response = $this->get(route('calendar.index'));

        $response->assertRedirect(route('login'));
    }
}
