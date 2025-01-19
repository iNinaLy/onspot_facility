<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $url;
    protected $secretKey;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->secretKey = config('services.supabase.secret');
        
        if (empty($this->url)) {
            \Log::error('Supabase URL is empty', ['url' => $this->url]);
            throw new \Exception("Supabase URL is not set in .env");
        }
        
        if (empty($this->secretKey)) {
            \Log::error('Supabase Secret Key is empty', ['key' => $this->secretKey]);
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

    public function update($table, $id, $data)
    {
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->patch(rtrim($this->url, '/') . "/rest/v1/{$table}?id=eq.{$id}", $data);

        if ($response->failed()) {
            throw new \Exception("Supabase error: " . $response->body());
        }

        return $response->json();
    }
}
