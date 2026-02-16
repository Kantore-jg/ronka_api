<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\PartnerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/gallery', [GalleryController::class, 'index']);
Route::get('/partners', [PartnerController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::post('/donations', [DonationController::class, 'store']);
Route::post('/partners', [PartnerController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events/{event}/comment', [EventController::class, 'addComment']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/donations', [DonationController::class, 'index']);
    Route::get('/feedbacks', [FeedbackController::class, 'index']);
    Route::post('/partners/{id}/approve', [PartnerController::class, 'approve']);

    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::delete('/members/{member}', [MemberController::class, 'destroy']);
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{event}/assign', [EventController::class, 'assignMember']);

    Route::post('/gallery', [GalleryController::class, 'store']);
    Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);
});
