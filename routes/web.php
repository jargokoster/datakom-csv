<?php

use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public: the docs UI; the API token is entered via the Swagger "Authorize" button.
Route::get('/docs', [DocsController::class, 'index']);
