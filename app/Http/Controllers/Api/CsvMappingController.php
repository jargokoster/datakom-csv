<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CsvMapping;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Column Mappings', description: 'Manage supplier CSV column mappings')]
final class CsvMappingController extends Controller
{
    #[OA\Get(path: '/column-mappings', summary: 'List column mappings', tags: ['Column Mappings'], responses: [new OA\Response(response: 200, description: 'Column mapping list', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/CsvMapping')))])]
    public function index()
    {
        return CsvMapping::query()->latest()->get();
    }

    #[OA\Post(
        path: '/column-mappings',
        summary: 'Create column mapping',
        tags: ['Column Mappings'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['supplier_id', 'source_column', 'target_column'],
                properties: [
                    new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
                    new OA\Property(property: 'source_column', type: 'string', example: 'unit_price'),
                    new OA\Property(property: 'target_column', type: 'string', example: 'price'),
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function store(Request $request)
    {
        $mapping = CsvMapping::create($request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'source_column' => ['required', 'string', 'max:255'],
            'target_column' => ['required', 'string', 'max:255'],
        ]));

        return response()->json($mapping, 201);
    }

    #[OA\Get(path: '/column-mappings/{column_mapping}', summary: 'Show column mapping', tags: ['Column Mappings'], parameters: [new OA\Parameter(name: 'column_mapping', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Details')])]
    public function show(CsvMapping $column_mapping)
    {
        return $column_mapping;
    }

    #[OA\Put(path: '/column-mappings/{column_mapping}', summary: 'Update column mapping', tags: ['Column Mappings'], parameters: [new OA\Parameter(name: 'column_mapping', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function update(Request $request, CsvMapping $column_mapping)
    {
        $column_mapping->update($request->validate([
            'source_column' => ['sometimes', 'string', 'max:255'],
            'target_column' => ['sometimes', 'string', 'max:255'],
        ]));

        return $column_mapping;
    }

    #[OA\Delete(path: '/column-mappings/{column_mapping}', summary: 'Delete column mapping', tags: ['Column Mappings'], parameters: [new OA\Parameter(name: 'column_mapping', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted')])]
    public function destroy(CsvMapping $column_mapping)
    {
        $column_mapping->delete();

        return response()->noContent();
    }
}
