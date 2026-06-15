<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['docs.token' => self::TOKEN]);
    }

    public function test_requires_a_token(): void
    {
        $this->getJson('/api/suppliers')->assertStatus(401);
    }

    public function test_lists_suppliers_without_pagination(): void
    {
        Supplier::create(['name' => 'Supplier A', 'active' => true]);
        Supplier::create(['name' => 'Supplier B', 'active' => false]);

        $response = $this->withToken(self::TOKEN)->getJson('/api/suppliers');

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([['id', 'name', 'active', 'created_at', 'updated_at']]);
    }

    public function test_creates_a_supplier(): void
    {
        $response = $this->withToken(self::TOKEN)
            ->postJson('/api/suppliers', ['name' => 'New Supplier', 'active' => true]);

        $response->assertCreated()->assertJsonPath('name', 'New Supplier');
        $this->assertDatabaseHas('suppliers', ['name' => 'New Supplier']);
    }

    public function test_validates_supplier_creation(): void
    {
        $this->withToken(self::TOKEN)
            ->postJson('/api/suppliers', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_shows_a_supplier(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);

        $this->withToken(self::TOKEN)
            ->getJson("/api/suppliers/{$supplier->id}")
            ->assertOk()
            ->assertJsonPath('id', $supplier->id);
    }

    public function test_updates_a_supplier(): void
    {
        $supplier = Supplier::create(['name' => 'Old name']);

        $this->withToken(self::TOKEN)
            ->putJson("/api/suppliers/{$supplier->id}", ['name' => 'New name'])
            ->assertOk()
            ->assertJsonPath('name', 'New name');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New name']);
    }

    public function test_deletes_a_supplier(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);

        $this->withToken(self::TOKEN)
            ->deleteJson("/api/suppliers/{$supplier->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
