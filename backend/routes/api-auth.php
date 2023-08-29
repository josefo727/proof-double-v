<?php

use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('api-auth.login');
/*
Route::get('/logout', LogoutController::class)
    ->middleware('auth:sanctum')
    ->name('json-api-auth.logout');

Route::post('/forgot-password', PasswordResetLinkController::class)
    ->name('json-api-auth.password.email');

Route::post('/reset-password', NewPasswordController::class)
    ->name('json-api-auth.password.update');

Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('json-api-auth.verification.send');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('json-api-auth.verification.verify');

Route::post('/confirm-password', ConfirmablePasswordController::class)
    ->middleware('auth:sanctum')
->name('json-api-auth.password.confirm');
*/
