<?php

namespace App\Services;

use App\DTO\EmployeeDTO;
use Illuminate\Http\Response;
use App\Models\ClientCredential;
use Illuminate\Support\Facades\Log;

class TrackTik
{
    protected $baseUrl, $client;

    public function __construct()
    {
        $this->baseUrl = config('services.track_tik.base_url');
        $this->client = new \Ixudra\Curl\CurlService();
    }

    protected function makeRequest($url, $payload, $method = "POST")
    {
        $token = $this->generateAccessToken();

        if (is_null($token)) {
            return response()->apiError(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        try {

            $response = $this->client->to($url)->withHeaders([
                "Authorization" => "Bearer " . $token,
                "Content-Type" => "application/json"
            ])->withData(json_encode($payload));

            switch ($method) {
                case 'PATCH':
                    $result = $response->patch();
                    break;
                default:
                    $result = $response->post();
                    break;
            }

            $result = json_decode($result);

            return response()->apiSuccess(Response::HTTP_OK, 'Successful...', $result);
        } catch (\Throwable $th) {
            return response()->apiError(Response::HTTP_INTERNAL_SERVER_ERROR, $th->getMessage());
        }
    }

    protected function generateAccessToken(): string|null
    {
        $credentials = ClientCredential::where('client_id', config('services.track_tik.client_id'))->first();

        if ($credentials) {
            $current_time = time();

            $expiry = (int)($credentials->expires_in / 1000);
            if ($current_time < $expiry) {
                return $credentials->access_token;
            }
        }

        $payload = [
            'grant_type' => "refresh_token",
            'client_secret' => config('services.track_tik.client_secret'),
            'client_id' => config('services.track_tik.client_id'),
            'refresh_token' => config('services.track_tik.refresh_token')
        ];

        $url = $this->baseUrl . "/oauth2/access_token";

        $response = $this->client->to($url)->withHeaders([
            'Content-Type' => 'application/json'
        ])->withData(json_encode($payload))->post();

        $result = json_decode($response);

        if ($result?->status !== Response::HTTP_OK) {
            Log::error("TrackTik Token Response: " . $result?->message);
            return null;
        }

        ClientCredential::updateOrCreate([
            'client_id' => config('services.track_tik.client_id')
        ], [
            'client_secret' => config('services.track_tik.client_secret'),
            'access_token' => $result->access_token,
            'expires_in' => $result->expires_in,
            'refresh_token' => $result->refresh_token
        ]);

        return $result->access_token;
    }

    public function create(EmployeeDTO $employeeDTO)
    {
        $payload = $employeeDTO->mapData();
        return $this->makeRequest($this->baseUrl . "/v1/employees", $payload);
    }

    public function update(EmployeeDTO $employeeDTO)
    {
        $payload = $employeeDTO->mapData();
        return $this->makeRequest($this->baseUrl . "/v1/employees/{$payload['id']}", $payload, "PATCH");
    }
}
