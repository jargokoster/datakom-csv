<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvProcessApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-token';

    private string $diskRoot;

    protected function setUp(): void
    {
        parent::setUp();
        config(['docs.token' => self::TOKEN]);

        // Use a writable temp dir for the "local" disk so the test does not
        // depend on storage/ ownership (which the Docker build chowns to www-data).
        $this->diskRoot = sys_get_temp_dir().'/datakom-test-'.uniqid();
        config(['filesystems.disks.local.root' => $this->diskRoot]);
    }

    protected function tearDown(): void
    {
        if (isset($this->diskRoot) && is_dir($this->diskRoot)) {
            File::deleteDirectory($this->diskRoot);
        }
        parent::tearDown();
    }

    public function test_requires_a_token(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);

        $this->postJson("/api/suppliers/{$supplier->id}/process-csv")->assertStatus(401);
    }

    public function test_validates_that_a_file_is_required(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);

        $this->withToken(self::TOKEN)
            ->postJson("/api/suppliers/{$supplier->id}/process-csv", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_processes_a_csv_applying_mapping_and_rules(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $supplier->columnMappings()->create(['source_column' => 'unit_price', 'target_column' => 'price']);
        $supplier->outputColumns()->create(['name' => 'price', 'order' => 1, 'active' => true, 'hidden' => false]);
        $supplier->processingRules()->create([
            'type' => 'multiply',
            'column_name' => 'price',
            'config' => ['factor' => 2],
            'order' => 1,
            'active' => true,
        ]);

        $csv = "unit_price\n10\n20\n";
        $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

        $response = $this->withToken(self::TOKEN)
            ->post("/api/suppliers/{$supplier->id}/process-csv", [
                'file' => $file,
                'save_file' => true,
            ]);

        $response->assertOk()
            ->assertJsonStructure(['job_id', 'export_path', 'rows']);

        // price column = mapped from unit_price, then multiplied by 2
        $this->assertSame('20', $response->json('rows.0.price'));
        $this->assertSame('40', $response->json('rows.1.price'));

        $this->assertDatabaseHas('csv_jobs', ['original_filename' => 'products.csv']);
        $this->assertTrue(Storage::disk('local')->exists($response->json('export_path')));
    }

    public function test_can_skip_writing_the_file(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $supplier->outputColumns()->create(['name' => 'sku', 'order' => 1, 'active' => true, 'hidden' => false]);

        $csv = "sku\nABC-1\n";
        $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

        $response = $this->withToken(self::TOKEN)
            ->post("/api/suppliers/{$supplier->id}/process-csv", [
                'file' => $file,
                'save_file' => false,
            ]);

        $response->assertOk()->assertJsonPath('export_path', null);
    }
}
