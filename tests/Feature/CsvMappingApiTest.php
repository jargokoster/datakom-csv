<?php

namespace Tests\Feature;

use App\Models\CsvMapping;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvMappingApiTest extends TestCase
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
        $this->getJson('/api/column-mappings')->assertStatus(401);
    }

    public function test_lists_column_mappings(): void
    {
        $supplier = $this->supplier();
        CsvMapping::create([
            'supplier_id' => $supplier->id,
            'source_column' => 'unit_price',
            'target_column' => 'price',
        ]);

        $this->withToken(self::TOKEN)->getJson('/api/column-mappings')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonStructure([['id', 'supplier_id', 'source_column', 'target_column']]);
    }

    public function test_creates_a_column_mapping(): void
    {
        $supplier = $this->supplier();

        $this->withToken(self::TOKEN)->postJson('/api/column-mappings', [
            'supplier_id' => $supplier->id,
            'source_column' => 'unit_price',
            'target_column' => 'price',
        ])->assertCreated()->assertJsonPath('target_column', 'price');

        $this->assertDatabaseHas('csv_mappings', ['source_column' => 'unit_price']);
    }

    public function test_validates_column_mapping_creation(): void
    {
        $this->withToken(self::TOKEN)->postJson('/api/column-mappings', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_id', 'source_column', 'target_column']);
    }

    public function test_shows_a_column_mapping(): void
    {
        $mapping = CsvMapping::create([
            'supplier_id' => $this->supplier()->id,
            'source_column' => 'unit_price',
            'target_column' => 'price',
        ]);

        $this->withToken(self::TOKEN)->getJson("/api/column-mappings/{$mapping->id}")
            ->assertOk()
            ->assertJsonPath('id', $mapping->id);
    }

    public function test_updates_a_column_mapping(): void
    {
        $mapping = CsvMapping::create([
            'supplier_id' => $this->supplier()->id,
            'source_column' => 'unit_price',
            'target_column' => 'price',
        ]);

        $this->withToken(self::TOKEN)->putJson("/api/column-mappings/{$mapping->id}", [
            'target_column' => 'net_price',
        ])->assertOk()->assertJsonPath('target_column', 'net_price');
    }

    public function test_deletes_a_column_mapping(): void
    {
        $mapping = CsvMapping::create([
            'supplier_id' => $this->supplier()->id,
            'source_column' => 'unit_price',
            'target_column' => 'price',
        ]);

        $this->withToken(self::TOKEN)->deleteJson("/api/column-mappings/{$mapping->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('csv_mappings', ['id' => $mapping->id]);
    }
}
