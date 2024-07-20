<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingsController extends Controller
{
    public function index()
    {
        return Bookings::with('room')->get();
    }
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Store Room Request: ', $request->all());
    
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'purpose' => 'required|string',
            'check_in' => 'required|date_format:Y-m-d H:i:s',
            'check_out' => 'required|date_format:Y-m-d H:i:s|after:check_in',
        ]);
    
        if ($validator->fails()) {
            Log::warning('Validation failed: ', $validator->messages()->toArray());
            return response()->json([
                'error' => 422,
                'message' => $validator->messages()
            ], 422);
        }
    
        // Check if the room is already booked for the given time range
        try {
            $overlappingBookings = Bookings::where('room_id', $request->room_id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                          ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                          ->orWhere(function($query) use ($request) {
                              $query->where('check_in', '<', $request->check_in)
                                    ->where('check_out', '>', $request->check_out);
                          });
                })
                ->exists();
    
            if ($overlappingBookings) {
                Log::info('Overlapping booking found for room ID: ' . $request->room_id);
                return response()->json([
                    'error' => 409,
                    'message' => 'The room is already booked for the selected time range',
                ], 409);
            }
    
            $booking = Bookings::create($validator->validated());
    
            Log::info('Room booked successfully: ', $booking->toArray());
            return response()->json([
                'status' => 200,
                'message' => 'Room booked successfully',
                'booking' => $booking,
            ], 200);
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Error while booking room: ', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return response()->json([
                'error' => 500,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
    public function showstaypeople($room_id)
    {
        // Fetch the room based on the room_id along with its bookings
        $room = Rooms::with('bookings')->findOrFail($room_id);
    
        // Return the room and bookings data as JSON
        return response()->json([
            // 'bookings' => $room->bookings,
            'room' => $room
        ]);
    }
    public function show(Bookings $booking)
    {
        return $booking->load('room');
    }
    public function update(Request $request, Bookings $booking)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'purpose' => 'required|string',
            'check_in' => 'required|date_format:Y-m-d H:i:s',
            'check_out' => 'required|date_format:Y-m-d H:i:s|after:check_in',
        ]);
    
        try {
            $booking->update($request->all());
            return response()->json(['success' => 'Booking Update Successfully', 'data' => $booking], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => 'Booking Update error', 'message' => $e->getMessage()], 500);
        }
    }
    public function destroy(Bookings $booking)
    {
        try {
            $booking->delete();
            return response()->json(['success' => true, 'message' => 'booking deleted successfully'], 204);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    

}
