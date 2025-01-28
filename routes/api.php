<?php

use App\Http\Controllers\API\LeadAPIController;

Route::prefix('leads')->group(function () {
    Route::get('/', [LeadAPIController::class, 'index']);
    Route::get('/{id}', [LeadAPIController::class, 'show']);
    Route::post('/', [LeadAPIController::class, 'store']);
    Route::put('/{id}', [LeadAPIController::class, 'update']);
    Route::delete('/{id}', [LeadAPIController::class, 'destroy']);
});
