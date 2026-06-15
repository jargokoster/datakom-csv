<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Supplier',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Supplier A'),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'CsvMapping',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
        new OA\Property(property: 'source_column', type: 'string', example: 'unit_price'),
        new OA\Property(property: 'target_column', type: 'string', example: 'price'),
    ]
)]
#[OA\Schema(
    schema: 'OutputColumn',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'price'),
        new OA\Property(property: 'order', type: 'integer', example: 10),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'hidden', type: 'boolean', example: false),
    ]
)]
#[OA\Schema(
    schema: 'ProcessingRule',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
        new OA\Property(property: 'type', type: 'string', enum: ['multiply', 'remove', 'regexp']),
        new OA\Property(property: 'column_name', type: 'string', example: 'price'),
        new OA\Property(property: 'config', type: 'object', example: ['factor' => 1.24]),
        new OA\Property(property: 'order', type: 'integer', example: 10),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ]
)]
final class Schemas
{
}
