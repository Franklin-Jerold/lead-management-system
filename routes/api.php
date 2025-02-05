<?php

use App\Http\Controllers\API\LeadAPIController;
use App\Http\Controllers\LeadImportController;


Route::prefix('leads')->group(function () {
    Route::post('/import-leads', [LeadImportController::class, 'import']);
    Route::get('/', [LeadAPIController::class, 'index']);
    Route::get('/{id}', [LeadAPIController::class, 'show']);
    Route::post('/', [LeadAPIController::class, 'store']);
    Route::put('/{id}', [LeadAPIController::class, 'update']);
    Route::delete('/{id}', [LeadAPIController::class, 'destroy']);
});
