<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $url;
    protected $secretKey;

    

    public function __construct()
    {
        $this->url = env('SUPABASE_URL');
        $this->secretKey = env('SUPABASE_SECRET_KEY');

        // Debugging: Throw an exception if the URL is missing
        if (empty($this->url)) {
            throw new \Exception("Supabase URL is not set in .env");
        }

        if (empty($this->secretKey)) {
            throw new \Exception("Supabase Secret Key is not set in .env");
        }
    }

    public function store($table, $data)
    {
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post(rtrim($this->url, '/') . "/rest/v1/{$table}", $data);
    
        if ($response->failed()) {
            throw new \Exception("Supabase error: " . $response->body());
        }
    
        return $response->json();
    }
    
}
