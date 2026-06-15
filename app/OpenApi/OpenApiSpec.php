<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.0',
    security: [['docsBearerAuth' => []]]
)]
#[OA\Info(
    version: '1.0.0',
    title: 'Datakom CSV Processor API',
    description: 'API for supplier CSV mapping, processing rules and CSV export.'
)]
#[OA\Server(
    url: 'http://localhost:8888/api',
    description: 'Local Docker API'
)]
#[OA\SecurityScheme(
    securityScheme: 'docsBearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
final class OpenApiSpec
{
}
