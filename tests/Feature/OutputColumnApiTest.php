<?php

namespace Tests\Feature;

use App\Models\OutputColumn;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutputColumnApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['docs.token' => self::TOKEN]);
    }

    private function supplier(): Supplier
    {
        return Supplier::create(['name' => 'Supplier A']);
    }

    public function test_requires_a_token(): void
    {
        $this->getJson('/api/output-columns')->assertStatus(401);
    }

    public function test_lists_output_columns_ordered(): void
    {
        $supplier = $this->supplier();
        OutputColumn::create(['supplier_id' => $supplier->id, 'name' => 'b', 'order' => 2]);
        OutputColumn::create(['supplier_id' => $supplier->id, 'name' => 'a', 'order' => 1]);

        $this->withToken(self::TOKEN)->getJson('/api/output-columns')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.name', 'a')
            ->assertJsonPath('1.name', 'b');
    }

    public function test_creates_an_output_column(): void
    {
        $supplier = $this->supplier();

        $this->withToken(self::TOKEN)->postJson('/api/output-columns', [
            'supplier_id' => $supplier->id,
            'name' => 'price',
            'order' => 10,
            'active' => true,
            'hidden' => false,
        ])->assertCreated()->assertJsonPath('name', 'price');

        $this->assertDatabaseHas('output_columns', ['name' => 'price']);
    }

    public function test_validates_output_column_creation(): void
    {
        $this->withToken(self::TOKEN)->postJson('/api/output-columns', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_id', 'name']);
    }

    public function test_shows_an_output_column(): void
    {
        $column = OutputColumn::create(['supplier_id' => $this->supplier()->id, 'name' => 'price']);

        $this->withToken(self::TOKEN)->getJson("/api/output-columns/{$column->id}")
            ->assertOk()
            ->assertJsonPath('id', $column->id);
    }

    public function test_updates_an_output_column(): void
    {
        $column = OutputColumn::create(['supplier_id' => $this->supplier()->id, 'name' => 'price']);

        $this->withToken(self::TOKEN)->putJson("/api/output-columns/{$column->id}", [
            'hidden' => true,
        ])->assertOk()->assertJsonPath('hidden', true);
    }

    public function test_deletes_an_output_column(): void
    {
        $column = OutputColumn::create(['supplier_id' => $this->supplier()->id, 'name' => 'price']);

        $this->withToken(self::TOKEN)->deleteJson("/api/output-columns/{$column->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('output_columns', ['id' => $column->id]);
    }
}
