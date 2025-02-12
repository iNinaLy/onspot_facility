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

    /**
     * Update or Insert data into Supabase (Prevent Duplicates)
     */
    public function store($table, $data)
    {
        // ✅ If `id` is provided, check if the record exists
        $queryParams = [];
        if (isset($data['id'])) {
            $queryParams[] = "id=eq.{$data['id']}";
        }
    
        $queryString = !empty($queryParams) ? '?' . implode('&', $queryParams) : '';
    
        // ✅ Step 1: Check if the record exists in Supabase
        $existingRecord = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get(rtrim($this->url, '/') . "/rest/v1/{$table}{$queryString}")
        ->json();
    
        // ✅ Step 2: If the record exists, update it
        if (!empty($existingRecord)) {
            $id = $existingRecord[0]['id']; // Get the existing record ID
            return $this->update($table, $id, $data);
        }
    
        // ✅ Step 3: If no record exists, insert a new one
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post(rtrim($this->url, '/') . "/rest/v1/{$table}", $data);
    
        if ($response->failed()) {
            throw new \Exception("Supabase insert error: " . $response->body());
        }
    
        return $response->json();
    }    

    /**
     * Update an existing record in Supabase
     */
    public function update($table, $id, $data)
    {
        $response = Http::withHeaders([
            'apikey' => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->patch(rtrim($this->url, '/') . "/rest/v1/{$table}?id=eq.{$id}", $data);

        if ($response->failed()) {
            throw new \Exception("Supabase update error: " . $response->body());
        }

        return $response->json();
    }
}
