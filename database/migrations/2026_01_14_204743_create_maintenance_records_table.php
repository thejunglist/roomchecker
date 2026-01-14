<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("maintenance_records", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("session_id")
                ->constrained("maintenance_sessions")
                ->onDelete("cascade");
            $table->foreignId("equipment_id")->constrained("equipment");
            $table->string("status");
            $table->text("notes")->nullable();
            $table->timestamp("checked_at")->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("maintenance_records");
    }
};
