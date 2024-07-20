<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoomsController extends Controller
{
    public function index()
    {
        return Rooms::all();
    }
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Store Room Request: ', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'capacity' => 'required|numeric|max:10',
            'phonenumber' => 'required|numeric|digits:10',
            'location' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 422,
                'message' => $validator->messages()
            ], 422);
        }

        try {
            $room = Rooms::create($validator->validated());

            return response()->json([
                'status' => 200,
                'message' => 'Room booked successfully',
                'room' => $room,
            ], 200);
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Error while booking room: ', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 500,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
    public function show(Rooms $room)
    {
        return $room;
    }

    public function update(Request $request, Rooms $room)
    {
        $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|numeric|max:10',
            'phonenumber' => 'required|numeric|digits:10',
            'location' => 'required|string'
        ]);
    
        try {
            $room->update($request->all());
            return response()->json(['success' => 'Room Update Successfully', 'data' => $room], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => 'Room Update error', 'message' => $e->getMessage()], 500);
        }
    }
    public function destroy(Rooms $room)
    {
        try {
            $room->delete();
            return response()->json(['success' => true, 'message' => 'Room deleted successfully'], 204);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


}
