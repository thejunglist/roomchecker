<?php
// app/Models/MaintenanceRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'equipment_id',
        'status',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(MaintenanceSession::class, 'session_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}