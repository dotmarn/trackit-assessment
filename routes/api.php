<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;

Route::prefix('/v1')->group(function () {

    Route::match(['post', 'patch'], '/employees', EmployeeController::class)->name('index');
    
});
