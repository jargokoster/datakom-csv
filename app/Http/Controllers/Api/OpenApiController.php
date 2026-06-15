<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

final class OpenApiController extends Controller
{
    public function __invoke()
    {
        return response()->file(
            storage_path('api-docs/openapi.json'),
            ['Content-Type' => 'application/json']
        );
    }
}
