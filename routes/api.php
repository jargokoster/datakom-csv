<?php

use App\Http\Controllers\Api\CsvMappingController;
use App\Http\Controllers\Api\CsvProcessController;
use App\Http\Controllers\Api\OpenApiController;
use App\Http\Controllers\Api\OutputColumnController;
use App\Http\Controllers\Api\ProcessingRuleController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Support\Facades\Route;

// Public: the OpenAPI spec so the docs UI can load it without a token.
Route::get('/openapi.json', OpenApiController::class);

Route::middleware('docs.auth')->group(function (): void {
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('column-mappings', CsvMappingController::class);
    Route::apiResource('output-columns', OutputColumnController::class);
    Route::apiResource('processing-rules', ProcessingRuleController::class);

    Route::post('/suppliers/{supplier}/process-csv', [CsvProcessController::class, 'store']);
});
