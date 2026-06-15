<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CsvJob;
use App\Models\Supplier;
use App\Services\Csv\CsvProcessor;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'CSV Processing', description: 'Process supplier CSV files')]
final class CsvProcessController extends Controller
{
    #[OA\Post(
        path: '/suppliers/{supplier}/process-csv',
        summary: 'Process supplier CSV',
        tags: ['CSV Processing'],
        security: [['docsBearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(property: 'file', type: 'string', format: 'binary'),
                        new OA\Property(property: 'save_file', type: 'boolean', example: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'CSV processed successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request, Supplier $supplier, CsvProcessor $processor)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'save_file' => ['nullable', 'boolean'],
        ]);

        $result = $processor->process(
            $supplier,
            $validated['file'],
            $request->boolean('save_file', true)
        );

        $job = CsvJob::create([
            'supplier_id' => $supplier->id,
            'original_filename' => $validated['file']->getClientOriginalName(),
            'export_path' => $result['export_path'],
            'processed_rows' => $result['rows'],
        ]);

        return response()->json([
            'job_id' => $job->id,
            'export_path' => $job->export_path,
            'rows' => $result['rows'],
        ]);
    }
}
