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

        if (empty($this->url)) {
            throw new \Exception("Supabase URL is not set in .env");
        }

        if (empty($this->secretKey)) {
            throw new \Exception("Supabase Secret Key is not set in .env");
        }
    }

    /**
     * Store data into the specified table.
     *
     * @param string $table
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function store($table, $data)
    {
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->post(rtrim($this->url, '/') . "/rest/v1/{$table}", $data);

        if ($response->failed()) {
            throw new \Exception("Supabase error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Update a record in the specified table.
     *
     * @param string $table
     * @param int|string $id
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function update($table, $id, array $data)
    {
        $url = rtrim($this->url, '/') . "/rest/v1/{$table}?id=eq.{$id}";
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->patch($url, $data);

        if ($response->failed()) {
            throw new \Exception("Supabase update error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Retrieve device tokens for a given user.
     *
     * @param int|string $userId
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getDeviceTokensForUser($userId)
    {
        $url = rtrim($this->url, '/') . "/rest/v1/notification_tokens?user_id=eq.{$userId}&select=device_token";
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->get($url);

        if ($response->failed()) {
            throw new \Exception("Supabase getDeviceTokensForUser error: " . $response->body());
        }

        $data = $response->json();
        return collect($data)->pluck('device_token');
    }

    /**
     * Retrieve all complaints.
     *
     * @return array
     * @throws \Exception
     */
    public function getComplaints()
    {
        $url = rtrim($this->url, '/') . "/rest/v1/complaints?select=*";
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->get($url);

        if ($response->failed()) {
            throw new \Exception("Supabase getComplaints error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Fetch all notifications from the Supabase "notifications" table.
     *
     * @return array
     * @throws \Exception
     */
    public function getNotifications()
    {
        $url = rtrim($this->url, '/') . "/rest/v1/notifications?select=*";
        
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->get($url);
        
        if ($response->failed()) {
            throw new \Exception("Supabase getNotifications error: " . $response->body());
        }
        
        return $response->json();
    }

    /**
     * Insert a new record into the "complaint_cleaner" table.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function storeComplaintCleaner(array $data)
    {
        $url = rtrim($this->url, '/') . "/rest/v1/complaint_cleaner";
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->post($url, [$data]); // Supabase expects an array of rows

        if ($response->failed()) {
            throw new \Exception("Supabase storeComplaintCleaner error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Delete a record from the specified table.
     *
     * @param string $table
     * @param int|string $id
     * @return array
     * @throws \Exception
     */
    public function delete($table, $id)
    {
        $url = rtrim($this->url, '/') . "/rest/v1/{$table}?id=eq.{$id}&select=*";
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->delete($url);

        if ($response->failed()) {
            throw new \Exception("Supabase delete error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Delete all pivot rows for a given complaint id in the complaint_cleaner table.
     *
     * @param int|string $complaintId
     * @return array
     * @throws \Exception
     */
    public function deleteComplaintCleanerByComplaintId($complaintId)
    {
        $url = rtrim($this->url, '/') . "/rest/v1/complaint_cleaner?complaint_id=eq.{$complaintId}&select=*";
        $response = Http::withHeaders([
            'apikey'        => $this->secretKey,
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->delete($url);

        if ($response->failed()) {
            throw new \Exception("Supabase deleteComplaintCleanerByComplaintId error: " . $response->body());
        }

        return $response->json();
    }
}
