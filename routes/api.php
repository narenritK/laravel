<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingLotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('nearest-parking-lots', [ParkingLotController::class, 'getNearestParkingLots']); //ค้นหาที่จอดรถใกล้user

Route::post('check-in', [ParkingLotController::class, 'checkIn']);

Route::post('check-out', [ParkingLotController::class, 'checkOut']);

