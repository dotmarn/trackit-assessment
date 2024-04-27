<?php

namespace App\Http\Controllers\Api;

use App\DTO\EmployeeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{
    public function __invoke(EmployeeRequest $request)
    {
        $payload = (new EmployeeDTO($request->validated()))->mapData();

        return $payload;        
    }

}
