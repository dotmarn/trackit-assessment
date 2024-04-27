<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;

Route::prefix('/v1')->group(function () {

    Route::post('/employees', [EmployeeController::class, 'create'])->name('create');
    
    Route::patch('/employees/{id}', [EmployeeController::class, 'update'])->name('update');
    
});
