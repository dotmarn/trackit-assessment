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
            ])->withData($payload)->returnResponseObject()->asJson();

            switch ($method) {
                case 'PATCH':
                    $result = $response->patch();
                    break;
                default:
                    $result = $response->post();
                    break;
            }

            if ($result->status === Response::HTTP_OK) {
                return response()->apiSuccess(Response::HTTP_OK, 'Successful...', $result->content);
            }

            return response()->apiError($result->status, $result->content->message);

        } catch (\Throwable $th) {
            return response()->apiError(Response::HTTP_INTERNAL_SERVER_ERROR, $th->getMessage());
        }
    }

    protected function generateAccessToken(): string|null
    {
        $credentials = ClientCredential::where('client_id', config('services.track_tik.client_id'))->first();

        if ($credentials) {
            $last_updated = $credentials->updated_at->addSeconds($credentials->expires_in);
            if (now()->lt($last_updated)) {
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
        ])->withData($payload)->returnResponseObject()->asJson()->post();

        if ($response->status !== Response::HTTP_OK) {
            Log::error("TrackTik Token Response: " . $response->content->message);
            return null;
        }

        ClientCredential::updateOrCreate([
            'client_id' => config('services.track_tik.client_id')
        ], [
            'client_secret' => config('services.track_tik.client_secret'),
            'access_token' => $response->content->access_token,
            'expires_in' => $response->content->expires_in,
            'refresh_token' => $response->content->refresh_token
        ]);

        return $response->content->access_token;
    }

}
