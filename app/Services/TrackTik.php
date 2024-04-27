<?php

namespace App\Services;

use App\DTO\EmployeeDTO;
use App\Models\ClientCredential;
use Illuminate\Http\Response;

class TrackTik
{
    protected $baseUrl, $client;

    public function __construct()
    {
        $this->baseUrl = config('services.track_tik.base_url');
        $this->client = new \Ixudra\Curl\CurlService();
    }

    protected function generateAccessToken()
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

        if (optional($result)->status !== Response::HTTP_OK) {
            return response()->apiError($result->status, $result->message);
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
}
