<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('visit/finish',[\App\Http\Controllers\ApiController::class, 'finishVisit'])->middleware('auth:sanctum');
