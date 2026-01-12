<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactFunction;
use App\Models\Country;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Entity $entity;
    protected ContactFunction $contactFunction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $country = Country::factory()->create();
        $this->entity = Entity::factory()->create(['country_id' => $country->id]);
        $this->contactFunction = ContactFunction::factory()->create();
    }

    /** @test */
    public function it_displays_contacts_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('contacts.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_displays_create_contact_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('contacts.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_contact()
    {
        $contactData = [
            'entity_id' => $this->entity->id,
            'name' => 'John',
            'surname' => 'Doe',
            'contact_function_id' => $this->contactFunction->id,
            'phone' => '210 123 456',
            'mobile' => '912 345 678',
            'email' => 'john.doe@example.com',
            'rgpd_consent' => true,
            'notes' => 'Test contact notes',
            'active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('contacts.store'), $contactData);

        $response->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'name' => 'John',
            'surname' => 'Doe',
        ]);

        // Verify encrypted email through model accessor
        $contact = Contact::where('name', 'John')->where('surname', 'Doe')->first();
        $this->assertEquals('john.doe@example.com', $contact->email);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->user)
            ->post(route('contacts.store'), []);

        $response->assertSessionHasErrors(['entity_id', 'name']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $response = $this->actingAs($this->user)
            ->post(route('contacts.store'), [
                'entity_id' => $this->entity->id,
                'name' => 'John',
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_displays_contact_show_page()
    {
        $contact = Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('contacts.show', $contact));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_displays_edit_contact_page()
    {
        $contact = Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('contacts.edit', $contact));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_update_a_contact()
    {
        $contact = Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('contacts.update', $contact), [
                'entity_id' => $this->entity->id,
                'name' => 'New Name',
                'surname' => 'New Surname',
                'active' => true,
            ]);

        $response->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'New Name',
            'surname' => 'New Surname',
        ]);
    }

    /** @test */
    public function it_can_delete_a_contact()
    {
        $contact = Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('contacts.destroy', $contact));

        $response->assertRedirect(route('contacts.index'));

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test */
    public function it_filters_contacts_by_entity()
    {
        $otherEntity = Entity::factory()->create(['country_id' => Country::factory()]);

        Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
        ]);

        Contact::factory()->create([
            'entity_id' => $otherEntity->id,
            'contact_function_id' => $this->contactFunction->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('contacts.index', ['entity_id' => $this->entity->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_searches_contacts_by_name()
    {
        Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
            'name' => 'Searchable',
            'surname' => 'Contact',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('contacts.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_contacts_when_entity_is_deleted()
    {
        $contact = Contact::factory()->create([
            'entity_id' => $this->entity->id,
            'contact_function_id' => $this->contactFunction->id,
        ]);

        $this->entity->delete();

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_contacts()
    {
        $response = $this->get(route('contacts.index'));

        $response->assertRedirect(route('login'));
    }
}
