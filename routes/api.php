<?php

use App\Http\Controllers\Api\BookingsController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('rooms',[RoomsController::class,'index']);
Route::post('rooms', [RoomsController::class, 'store']);
Route::get('/rooms/{room}', [RoomsController::class, 'show']);
Route::put('/rooms/{room}', [RoomsController::class, 'update']);
Route::delete('/rooms/{room}', [RoomsController::class, 'destroy']);

Route::get('bookings', [BookingsController::class, 'index']);
Route::post('bookings', [BookingsController::class, 'store']);
Route::get('bookings/{booking}', [BookingsController::class, 'show']);
Route::get('bookings/room/{room_id}', [BookingsController::class, 'showstaypeople']);
Route::put('bookings/{booking}', [BookingsController::class, 'update']);
Route::delete('bookings/{booking}', [BookingsController::class, 'destroy']);

