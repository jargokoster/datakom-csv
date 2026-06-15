<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Suppliers', description: 'Manage suppliers')]
final class SupplierController extends Controller
{
    #[OA\Get(
        path: '/suppliers',
        summary: 'List suppliers',
        tags: ['Suppliers'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Supplier list',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Supplier'))
            )
        ]
    )]
    public function index()
    {
        return Supplier::query()->latest()->get();
    }

    #[OA\Post(
        path: '/suppliers',
        summary: 'Create supplier',
        tags: ['Suppliers'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Supplier A'),
                    new OA\Property(property: 'active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Supplier created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $supplier = Supplier::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]));

        return response()->json($supplier, 201);
    }

    #[OA\Get(
        path: '/suppliers/{supplier}',
        summary: 'Show supplier',
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Supplier details', content: new OA\JsonContent(ref: '#/components/schemas/Supplier')),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    #[OA\Put(
        path: '/suppliers/{supplier}',
        summary: 'Update supplier',
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Updated Supplier'),
                    new OA\Property(property: 'active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Supplier updated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
        ]));

        return $supplier;
    }

    #[OA\Delete(
        path: '/suppliers/{supplier}',
        summary: 'Delete supplier',
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Supplier deleted'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->noContent();
    }
}
