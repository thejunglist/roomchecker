<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model // ← Fixed!
{
    use HasFactory;

    protected $fillable = ["name", "description", "maintenance_frequency_days"];

    protected $casts = [
        "maintenance_frequency_days" => "integer",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    // Relationships
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
