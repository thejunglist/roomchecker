<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\EquipmentType;
use App\Models\Equipment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'email' => 'admin@strathclyde.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);

        // Create technician
        $tech = User::create([
            'email' => 'tech@strathclyde.ac.uk',
            'password' => Hash::make('password'),
            'role' => 'technician',
            'first_name' => 'Tech',
            'last_name' => 'User',
        ]);

        // Create rooms
        $gh701 = Room::create([
            'room_code' => 'GH701',
            'building' => 'Graham Hills Building',
            'floor' => 7,
            'description' => 'Teaching room (flexible seating)',
        ]);

        $gh702 = Room::create([
            'room_code' => 'GH702',
            'building' => 'Graham Hills Building',
            'floor' => 7,
            'description' => 'Teaching room (flexible seating)',
        ]);

        // Create equipment types
        $projector = EquipmentType::create([
            'name' => 'Projector',
            'description' => 'Digital projector',
            'maintenance_frequency_days' => 30,
        ]);

        $microphone = EquipmentType::create([
            'name' => 'Microphone',
            'description' => 'Wireless microphone',
            'maintenance_frequency_days' => 30,
        ]);

        $screen = EquipmentType::create([
            'name' => 'Screen',
            'description' => 'Projection screen',
            'maintenance_frequency_days' => 90,
        ]);

        // Create equipment
        Equipment::create([
            'asset_number' => 'PROJ-001',
            'equipment_type_id' => $projector->id,
            'room_id' => $gh701->id,
            'manufacturer' => 'Epson',
            'model' => 'EB-2250U',
            'status' => 'active',
        ]);

        Equipment::create([
            'asset_number' => 'MIC-001',
            'equipment_type_id' => $microphone->id,
            'room_id' => $gh701->id,
            'manufacturer' => 'Shure',
            'model' => 'SM58',
            'status' => 'active',
        ]);

        Equipment::create([
            'asset_number' => 'SCR-001',
            'equipment_type_id' => $screen->id,
            'room_id' => $gh701->id,
            'manufacturer' => 'Da-Lite',
            'status' => 'active',
        ]);

        Equipment::create([
            'asset_number' => 'PROJ-002',
            'equipment_type_id' => $projector->id,
            'room_id' => $gh702->id,
            'manufacturer' => 'Epson',
            'model' => 'EB-2250U',
            'status' => 'active',
        ]);
    }
}
