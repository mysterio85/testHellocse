<?php

use App\Domain\Administrator\Http\Controllers\Api\AuthAdminController;
use App\Domain\Comment\Http\Controllers\Api\CommentController;
use App\Domain\Profile\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthAdminController::class, 'login']);

Route::get('/profiles', [ProfileController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profiles', [ProfileController::class, 'store']);
    Route::put('/profiles/{profile}', [ProfileController::class, 'update']);
    Route::delete('/profiles/{profile}', [ProfileController::class, 'destroy']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::post('/logout', [AuthAdminController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json([
        'user'  => $request->user(),
        'token' => $request->bearerToken(),
    ]);
});
