<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;

class MaintenanceRecordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "session_id" => "required|exists:maintenance_sessions,id",
            "equipment_id" => "required|exists:equipment,id",
            "status" => "required|in:pass,fail,needs_attention",
            "notes" => "nullable|string",
        ]);

        $record = MaintenanceRecord::create($request->all());
        $record->load("equipment");

        return response()->json($record, 201);
    }
}
