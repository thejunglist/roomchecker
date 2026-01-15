<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms');
            $table->foreignId('technician_user_id')->constrained('users');
            $table->date('session_date');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status')->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_sessions');
    }
};