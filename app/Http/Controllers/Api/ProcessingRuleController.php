<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcessingRule;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Processing Rules', description: 'Manage supplier CSV processing rules')]
final class ProcessingRuleController extends Controller
{
    #[OA\Get(path: '/processing-rules', summary: 'List processing rules', tags: ['Processing Rules'], responses: [new OA\Response(response: 200, description: 'Processing rule list', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ProcessingRule')))])]
    public function index()
    {
        return ProcessingRule::query()->orderBy('order')->get();
    }

    #[OA\Post(
        path: '/processing-rules',
        summary: 'Create processing rule',
        tags: ['Processing Rules'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['supplier_id', 'type', 'column_name'],
                properties: [
                    new OA\Property(property: 'supplier_id', type: 'integer', example: 1),
                    new OA\Property(property: 'type', type: 'string', enum: ['multiply', 'remove', 'regexp'], example: 'multiply'),
                    new OA\Property(property: 'column_name', type: 'string', example: 'price'),
                    new OA\Property(property: 'config', type: 'object', example: ['factor' => 1.24]),
                    new OA\Property(property: 'order', type: 'integer', example: 10),
                    new OA\Property(property: 'active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function store(Request $request)
    {
        $rule = ProcessingRule::create($request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'type' => ['required', 'string', 'in:multiply,remove,regexp'],
            'column_name' => ['required', 'string', 'max:255'],
            'config' => ['nullable', 'array'],
            'order' => ['nullable', 'integer'],
            'active' => ['nullable', 'boolean'],
        ]));

        return response()->json($rule, 201);
    }

    #[OA\Get(path: '/processing-rules/{processing_rule}', summary: 'Show processing rule', tags: ['Processing Rules'], parameters: [new OA\Parameter(name: 'processing_rule', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Details', content: new OA\JsonContent(ref: '#/components/schemas/ProcessingRule')), new OA\Response(response: 404, description: 'Not found')])]
    public function show(ProcessingRule $processing_rule)
    {
        return $processing_rule;
    }

    #[OA\Put(path: '/processing-rules/{processing_rule}', summary: 'Update processing rule', tags: ['Processing Rules'], parameters: [new OA\Parameter(name: 'processing_rule', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Updated'), new OA\Response(response: 404, description: 'Not found'), new OA\Response(response: 422, description: 'Validation error')])]
    public function update(Request $request, ProcessingRule $processing_rule)
    {
        $processing_rule->update($request->validate([
            'type' => ['sometimes', 'string', 'in:multiply,remove,regexp'],
            'column_name' => ['sometimes', 'string', 'max:255'],
            'config' => ['sometimes', 'array'],
            'order' => ['sometimes', 'integer'],
            'active' => ['sometimes', 'boolean'],
        ]));

        return $processing_rule;
    }

    #[OA\Delete(path: '/processing-rules/{processing_rule}', summary: 'Delete processing rule', tags: ['Processing Rules'], parameters: [new OA\Parameter(name: 'processing_rule', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(response: 404, description: 'Not found')])]
    public function destroy(ProcessingRule $processing_rule)
    {
        $processing_rule->delete();

        return response()->noContent();
    }
}
