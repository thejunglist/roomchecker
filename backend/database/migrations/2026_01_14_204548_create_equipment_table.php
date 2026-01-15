<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("equipment", function (Blueprint $table) {
            $table->id();
            $table->string("asset_number")->unique();
            $table
                ->foreignId("equipment_type_id")
                ->constrained("equipment_types");
            $table
                ->foreignId("room_id")
                ->nullable()
                ->constrained("rooms")
                ->nullOnDelete();
            $table->string("manufacturer")->nullable();
            $table->string("model")->nullable();
            $table->string("serial_number")->nullable();
            $table->date("purchase_date")->nullable();
            $table->string("status")->default("active");
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("equipment");
    }
};
