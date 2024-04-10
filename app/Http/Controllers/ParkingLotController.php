<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CarPark;
use App\Models\checkin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingLotController extends Controller
{
    public function getNearestParkingLots(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $name = $request->input('name');

        // Retrieve user by name
        $user = User::where('name', $name)->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $userLatitude = $user->lat;
        $userLongitude = $user->long;
        
        $nearestParkingLots = CarPark::select('id', 'name', 'latitude', 'longitude')
        ->selectRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$userLatitude, $userLongitude, $userLatitude]
        )
        ->orderBy('distance')
        ->limit(5) // Limit to 5 nearest parking lots
        ->get();

    if ($nearestParkingLots->isEmpty()) {
        return response()->json(['error' => 'No nearest parking lots found'], 404);
    }

        return response()->json($nearestParkingLots);
    }

    
    
    public function checkIn(Request $request)
    {
        // Validate input
        $request->validate([
            'iduser' => 'required|integer',
            'idpark' => 'required|integer',
        ]);

        $userId = $request->input('iduser');
        $parkId = $request->input('idpark');
        // Retrieve parking lot by ID
        $user = User::find($userId);
        $parkingLot = CarPark::find($parkId);      

        if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
        }

        if (!$parkingLot) {
            return response()->json(['error' => 'Parking lot not found'], 404);
        }

        $checkIn = new CheckIn();
        $checkIn->id_user = $userId;
        $checkIn->name_user = $user->name;
        $checkIn->name_park = $parkingLot->name;
        $checkIn->id_park = $parkId;
        $checkIn->checkin = now();
        $checkIn->save();

        return response()->json(['message' => 'Check-in successful']);
    }


    public function checkOut(Request $request)
    {
        // Validate input
        $request->validate([
            'iduser' => 'required|integer',
        ]);

        $userId = $request->input('iduser');

        // Check if user has checked in
        $checkIn = CheckIn::where('id_user', $userId)->first();

        if (!$checkIn) {
            return response()->json(['error' => 'User has not checked in'], 404);
        }

        $checkInTime = strtotime($checkIn->checkin);
        $checkOutTime = time();
        $parkingDurationMinutes = ceil(($checkOutTime - $checkInTime) / 60); // Round up to nearest minute
        $id_park = $checkIn->id_park;
        $parkID = CarPark::find($id_park);  
        // Calculate parking fee based on the rule (first 15 minutes free, then 20 baht per hour)
        $firstFreeMinutes = 15;
        //$hourlyRate = 20;
        $totalFee = 0;

        if ($parkingDurationMinutes > $firstFreeMinutes) {
            // Calculate fee for exceeded duration
            $exceededMinutes = $parkingDurationMinutes - $firstFreeMinutes;
            $exceededHours = ceil($exceededMinutes / 60); // Round up to nearest hour
            $totalFee = $exceededHours * $parkID->cost;
        }

        // Perform check-out
        $checkIn->checkout = now(); // Set checkout time to current datetime
        $checkIn->cost = $totalFee;
        $checkIn->save();

        return response()->json([
            'message' => 'Check-out successful',
            'parking_duration' => $parkingDurationMinutes,
            'total_fee' => $totalFee,
        ]);
    }

}
