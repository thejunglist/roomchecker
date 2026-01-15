<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\MaintenanceSessionController;
use App\Http\Controllers\Api\MaintenanceRecordController;

// Public routes
Route::post("/login", [AuthController::class, "login"]);

// Protected routes
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/me", [AuthController::class, "me"]);

    Route::get("/rooms", [RoomController::class, "index"]);
    Route::get("/rooms/{id}", [RoomController::class, "show"]);

    Route::get("/maintenance-sessions", [
        MaintenanceSessionController::class,
        "index",
    ]);
    Route::post("/maintenance-sessions", [
        MaintenanceSessionController::class,
        "store",
    ]);
    Route::get("/maintenance-sessions/{id}", [
        MaintenanceSessionController::class,
        "show",
    ]);
    Route::put("/maintenance-sessions/{id}", [
        MaintenanceSessionController::class,
        "update",
    ]);

    Route::post("/maintenance-records", [
        MaintenanceRecordController::class,
        "store",
    ]);
});
