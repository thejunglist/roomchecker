<?php
// app/Models/Room.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_code',
        'building',
        'floor',
        'description',
    ];

    protected $casts = [
        'floor' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function maintenanceSessions()
    {
        return $this->hasMany(MaintenanceSession::class);
    }

    public function faults()
    {
        return $this->hasMany(Fault::class);
    }
}