<?php
// app/Models/MaintenanceSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'technician_user_id',
        'session_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_user_id');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'session_id');
    }
}