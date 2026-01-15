<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function maintenanceSessions()
    {
        return $this->hasMany(MaintenanceSession::class, 'technician_user_id');
    }

    public function reportedFaults()
    {
        return $this->hasMany(Fault::class, 'reported_by_user_id');
    }

    public function assignedFaults()
    {
        return $this->hasMany(Fault::class, 'assigned_to_user_id');
    }
}