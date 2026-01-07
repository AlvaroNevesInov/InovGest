<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->country = Country::factory()->create();
    }

    /** @test */
    public function it_displays_entities_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('entities.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_displays_create_entity_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('entities.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_client()
    {
        $entityData = [
            'type' => 'client',
            'nif' => '123456789',
            'name' => 'Test Client Ltd',
            'address' => 'Test Street 123',
            'postal_code' => '1234-567',
            'city' => 'Lisbon',
            'country_id' => $this->country->id,
            'phone' => '210 123 456',
            'mobile' => '912 345 678',
            'email' => 'client@example.com',
            'website' => 'https://example.com',
            'rgpd_consent' => true,
            'notes' => 'Test notes',
            'active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('entities.store'), $entityData);

        $response->assertRedirect(route('entities.index'));

        $this->assertDatabaseHas('entities', [
            'nif' => '123456789',
            'name' => 'Test Client Ltd',
            'type' => 'client',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->user)
            ->post(route('entities.store'), []);

        $response->assertSessionHasErrors(['type', 'nif', 'name']);
    }

    /** @test */
    public function it_validates_nif_uniqueness()
    {
        Entity::factory()->create([
            'nif' => '123456789',
            'country_id' => $this->country->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('entities.store'), [
                'type' => 'client',
                'nif' => '123456789',
                'name' => 'Test Client',
            ]);

        $response->assertSessionHasErrors(['nif']);
    }

    /** @test */
    public function it_validates_postal_code_format()
    {
        $response = $this->actingAs($this->user)
            ->post(route('entities.store'), [
                'type' => 'client',
                'nif' => '123456789',
                'name' => 'Test Client',
                'postal_code' => 'invalid',
            ]);

        $response->assertSessionHasErrors(['postal_code']);
    }

    /** @test */
    public function it_displays_entity_show_page()
    {
        $entity = Entity::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('entities.show', $entity));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_displays_edit_entity_page()
    {
        $entity = Entity::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('entities.edit', $entity));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_update_an_entity()
    {
        $entity = Entity::factory()->create([
            'country_id' => $this->country->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('entities.update', $entity), [
                'type' => 'supplier',
                'nif' => $entity->nif,
                'name' => 'New Name',
                'active' => true,
            ]);

        $response->assertRedirect(route('entities.index'));

        $this->assertDatabaseHas('entities', [
            'id' => $entity->id,
            'name' => 'New Name',
            'type' => 'supplier',
        ]);
    }

    /** @test */
    public function it_can_delete_an_entity()
    {
        $entity = Entity::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('entities.destroy', $entity));

        $response->assertRedirect(route('entities.index'));

        $this->assertDatabaseMissing('entities', [
            'id' => $entity->id,
        ]);
    }

    /** @test */
    public function it_filters_entities_by_type()
    {
        Entity::factory()->client()->create(['country_id' => $this->country->id]);
        Entity::factory()->supplier()->create(['country_id' => $this->country->id]);

        $response = $this->actingAs($this->user)
            ->get(route('entities.index', ['type' => 'client']));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_searches_entities_by_name()
    {
        Entity::factory()->create([
            'name' => 'Searchable Entity',
            'country_id' => $this->country->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('entities.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_entities()
    {
        $response = $this->get(route('entities.index'));

        $response->assertRedirect(route('login'));
    }
}
