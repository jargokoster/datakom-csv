<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Generator;

final class GenerateOpenApi extends Command
{
    protected $signature = 'openapi:generate';

    protected $description = 'Generate OpenAPI documentation from PHP attributes';

    public function handle(): int
    {
        if (! is_dir(storage_path('api-docs'))) {
            mkdir(storage_path('api-docs'), 0755, true);
        }

        $openapi = (new Generator())->generate([
            app_path(),
        ]);

        file_put_contents(
            storage_path('api-docs/openapi.json'),
            $openapi->toJson()
        );

        $this->info('OpenAPI documentation generated.');

        return self::SUCCESS;
    }
}
