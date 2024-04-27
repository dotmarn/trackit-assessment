<?php

namespace App\Http\Controllers\Api;

use App\DTO\EmployeeDTO;
use App\Services\TrackTik;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{
    public function __invoke(EmployeeRequest $request, TrackTik $trackTik) : \Illuminate\Http\JsonResponse
    {
        $payload = new EmployeeDTO($request->validated()); 
        $response = $request->method() === 'POST' ? $trackTik->create($payload) : $trackTik->update($payload);
        return $response;
    }

}
