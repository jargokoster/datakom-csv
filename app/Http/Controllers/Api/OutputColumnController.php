<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutputColumn;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Output Columns', description: 'Manage exported CSV columns')]
final class OutputColumnController extends Controller
{
    #[OA\Get(path: '/output-columns', summary: 'List output columns', tags: ['Output Columns'], responses: [new OA\Response(response: 200, description: 'Output column list', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/OutputColumn')))])]
    public function index()
    {
        return OutputColumn::query()->orderBy('order')->get();
    }

    #[OA\Post(
        path: '/output-columns',
        summary: 'Create output column',
        tags: ['Output Columns'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['supplier_id', 'name'],
                properties: [
                    new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'price'),
                    new OA\Property(property: 'order', type: 'integer', example: 10),
                    new OA\Property(property: 'active', type: 'boolean', example: true),
                    new OA\Property(property: 'hidden', type: 'boolean', example: false),
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function store(Request $request)
    {
        $column = OutputColumn::create($request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
            'active' => ['nullable', 'boolean'],
            'hidden' => ['nullable', 'boolean'],
        ]));

        return response()->json($column, 201);
    }

    #[OA\Get(path: '/output-columns/{output_column}', summary: 'Show output column', tags: ['Output Columns'], parameters: [new OA\Parameter(name: 'output_column', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Details')])]
    public function show(OutputColumn $output_column)
    {
        return $output_column;
    }

    #[OA\Put(path: '/output-columns/{output_column}', summary: 'Update output column', tags: ['Output Columns'], parameters: [new OA\Parameter(name: 'output_column', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function update(Request $request, OutputColumn $output_column)
    {
        $output_column->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'order' => ['sometimes', 'integer'],
            'active' => ['sometimes', 'boolean'],
            'hidden' => ['sometimes', 'boolean'],
        ]));

        return $output_column;
    }

    #[OA\Delete(path: '/output-columns/{output_column}', summary: 'Delete output column', tags: ['Output Columns'], parameters: [new OA\Parameter(name: 'output_column', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted')])]
    public function destroy(OutputColumn $output_column)
    {
        $output_column->delete();

        return response()->noContent();
    }
}
