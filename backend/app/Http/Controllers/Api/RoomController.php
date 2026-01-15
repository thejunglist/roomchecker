<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount("equipment")->get();

        return response()->json($rooms);
    }

    public function show($id)
    {
        $room = Room::with(["equipment.equipmentType"])->findOrFail($id);

        return response()->json($room);
    }
}
