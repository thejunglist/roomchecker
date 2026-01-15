<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSession;
use Illuminate\Http\Request;

class MaintenanceSessionController extends Controller
{
    public function index()
    {
        $sessions = MaintenanceSession::with(['room', 'technician', 'maintenanceRecords'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $session = MaintenanceSession::create([
            'room_id' => $request->room_id,
            'technician_user_id' => $request->user()->id,
            'session_date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        $session->load(['room', 'technician']);

        return response()->json($session, 201);
    }

    public function show($id)
    {
        $session = MaintenanceSession::with(['room', 'technician', 'maintenanceRecords.equipment'])
            ->findOrFail($id);
        
        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $session = MaintenanceSession::findOrFail($id);

        $request->validate([
            'status' => 'required|in:in_progress,completed,abandoned',
            'notes' => 'nullable|string',
        ]);

        $session->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'end_time' => $request->status !== 'in_progress' ? now() : null,
        ]);

        $session->load(['room', 'technician', 'maintenanceRecords']);

        return response()->json($session);
    }
}