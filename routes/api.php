<?php

use App\Http\Controllers\V1\GoalController;
use App\Http\Controllers\V1\ScheduleController;
use App\Http\Controllers\V1\StudyBlockController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/goals', GoalController::class);
    Route::apiResource('/schedules', ScheduleController::class);
    Route::apiResource('/study-blocks', StudyBlockController::class);
    Route::put('/study-blocks/{id}/atualizar', [StudyBlockController::class, 'updateCompleted']);
});
