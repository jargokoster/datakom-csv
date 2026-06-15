<?php

namespace Tests\Feature;

use App\Models\ProcessingRule;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessingRuleApiTest extends TestCase
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
        $this->getJson('/api/processing-rules')->assertStatus(401);
    }

    public function test_lists_processing_rules_ordered(): void
    {
        $supplier = $this->supplier();
        ProcessingRule::create(['supplier_id' => $supplier->id, 'type' => 'remove', 'column_name' => 'sku', 'order' => 2]);
        ProcessingRule::create(['supplier_id' => $supplier->id, 'type' => 'multiply', 'column_name' => 'price', 'order' => 1]);

        $this->withToken(self::TOKEN)->getJson('/api/processing-rules')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.column_name', 'price')
            ->assertJsonStructure([['id', 'supplier_id', 'type', 'column_name', 'config', 'order', 'active']]);
    }

    public function test_creates_a_processing_rule(): void
    {
        $supplier = $this->supplier();

        $this->withToken(self::TOKEN)->postJson('/api/processing-rules', [
            'supplier_id' => $supplier->id,
            'type' => 'multiply',
            'column_name' => 'price',
            'config' => ['factor' => 1.24],
            'order' => 10,
            'active' => true,
        ])->assertCreated()->assertJsonPath('type', 'multiply');

        $this->assertDatabaseHas('processing_rules', ['column_name' => 'price', 'type' => 'multiply']);
    }

    public function test_validates_processing_rule_creation(): void
    {
        $this->withToken(self::TOKEN)->postJson('/api/processing-rules', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_id', 'type', 'column_name']);
    }

    public function test_rejects_an_unknown_rule_type(): void
    {
        $supplier = $this->supplier();

        $this->withToken(self::TOKEN)->postJson('/api/processing-rules', [
            'supplier_id' => $supplier->id,
            'type' => 'bogus',
            'column_name' => 'price',
        ])->assertStatus(422)->assertJsonValidationErrors('type');
    }

    public function test_shows_a_processing_rule(): void
    {
        $rule = ProcessingRule::create(['supplier_id' => $this->supplier()->id, 'type' => 'remove', 'column_name' => 'sku']);

        $this->withToken(self::TOKEN)->getJson("/api/processing-rules/{$rule->id}")
            ->assertOk()
            ->assertJsonPath('id', $rule->id);
    }

    public function test_updates_a_processing_rule(): void
    {
        $rule = ProcessingRule::create(['supplier_id' => $this->supplier()->id, 'type' => 'multiply', 'column_name' => 'price']);

        $this->withToken(self::TOKEN)->putJson("/api/processing-rules/{$rule->id}", [
            'active' => false,
        ])->assertOk()->assertJsonPath('active', false);
    }

    public function test_deletes_a_processing_rule(): void
    {
        $rule = ProcessingRule::create(['supplier_id' => $this->supplier()->id, 'type' => 'remove', 'column_name' => 'sku']);

        $this->withToken(self::TOKEN)->deleteJson("/api/processing-rules/{$rule->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('processing_rules', ['id' => $rule->id]);
    }
}
