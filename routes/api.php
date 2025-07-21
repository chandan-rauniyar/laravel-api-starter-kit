<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [TestController::class, 'test']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/set-new-password', [AuthController::class, 'setNewPassword']);
Route::post('/email/verify/send', [AuthController::class, 'sendEmailVerification']);
Route::post('/email/verify', [AuthController::class, 'verifyEmail']);
