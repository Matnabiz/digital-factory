<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DesignController;
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::middleware('auth:sanctum')->group(function () {
 Route::get('/me', [AuthController::class,'me']);
 Route::post('/logout', [AuthController::class,'logout']);
 Route::get('/stages', [DesignController::class,'stages']);
 Route::get('/sessions', [DesignController::class,'index']);
 Route::post('/sessions', [DesignController::class,'store']);
 Route::get('/sessions/{session}', [DesignController::class,'show']);
 Route::post('/sessions/{session}/decisions', [DesignController::class,'decide']);
 Route::get('/sessions/{session}/model', [DesignController::class,'model']);
 Route::get('/sessions/{session}/report', [DesignController::class,'report']);
});
