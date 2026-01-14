<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("faults", function (Blueprint $table) {
            $table->id();
            $table->foreignId("equipment_id")->constrained("equipment");
            $table->foreignId("room_id")->constrained("rooms");
            $table->foreignId("reported_by_user_id")->constrained("users");
            $table
                ->foreignId("assigned_to_user_id")
                ->nullable()
                ->constrained("users");
            $table->string("status")->default("open");
            $table->string("severity")->nullable();
            $table->text("description");
            $table->text("resolution_notes")->nullable();
            $table->timestamp("resolved_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("faults");
    }
};
