<?php

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\Entity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class EntityModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_encrypts_sensitive_fields()
    {
        $entity = Entity::factory()->create([
            'email' => 'test@example.com',
            'phone' => '123456789',
            'mobile' => '987654321',
        ]);

        // Refresh from database to get raw values
        $entity->refresh();

        // Email, phone, and mobile should be accessible normally
        $this->assertEquals('test@example.com', $entity->email);
        $this->assertEquals('123456789', $entity->phone);
        $this->assertEquals('987654321', $entity->mobile);
    }

    /** @test */
    public function it_does_not_encrypt_nif()
    {
        $entity = Entity::factory()->create([
            'nif' => '123456789',
        ]);

        // NIF should not be encrypted
        $rawEntity = \DB::table('entities')->find($entity->id);
        $this->assertEquals('123456789', $rawEntity->nif);
    }

    /** @test */
    public function it_has_country_relationship()
    {
        $entity = Entity::factory()->create();

        $this->assertInstanceOf(\App\Models\Country::class, $entity->country);
    }

    /** @test */
    public function it_has_contacts_relationship()
    {
        $entity = Entity::factory()->create();
        Contact::factory()->count(3)->create(['entity_id' => $entity->id]);

        $this->assertCount(3, $entity->contacts);
        $this->assertInstanceOf(Contact::class, $entity->contacts->first());
    }

    /** @test */
    public function it_scopes_clients_correctly()
    {
        Entity::factory()->client()->create();
        Entity::factory()->supplier()->create();
        Entity::factory()->create(['type' => 'both']);

        $clients = Entity::clients()->get();

        $this->assertCount(2, $clients); // client and both
    }

    /** @test */
    public function it_scopes_suppliers_correctly()
    {
        Entity::factory()->client()->create();
        Entity::factory()->supplier()->create();
        Entity::factory()->create(['type' => 'both']);

        $suppliers = Entity::suppliers()->get();

        $this->assertCount(2, $suppliers); // supplier and both
    }

    /** @test */
    public function it_scopes_active_entities()
    {
        Entity::factory()->count(3)->create(['active' => true]);
        Entity::factory()->count(2)->create(['active' => false]);

        $activeEntities = Entity::active()->get();

        $this->assertCount(3, $activeEntities);
    }

    /** @test */
    public function it_generates_incremental_numbers()
    {
        $entity1 = Entity::factory()->create();
        $entity2 = Entity::factory()->create();
        $entity3 = Entity::factory()->create();

        // Check that numbers are incremental (not absolute values)
        $this->assertTrue($entity2->number > $entity1->number);
        $this->assertTrue($entity3->number > $entity2->number);
        $this->assertEquals($entity1->number + 1, $entity2->number);
        $this->assertEquals($entity2->number + 1, $entity3->number);
    }

    /** @test */
    public function it_casts_boolean_fields()
    {
        $entity = Entity::factory()->create([
            'rgpd_consent' => true,
            'active' => false,
        ]);

        $this->assertIsBool($entity->rgpd_consent);
        $this->assertIsBool($entity->active);
        $this->assertTrue($entity->rgpd_consent);
        $this->assertFalse($entity->active);
    }
}
