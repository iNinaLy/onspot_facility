<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create sample officer users and insert into users table
        $officers = [
            [
                'name' => 'Aminah',
                'username' => 'aminah',
                'email' => 'aminah@gmail.com',
                'password' => Hash::make('password123'), // Hashed password
                'role' => 'officer', // Ensure the role is set to officer
                'phone_no' => '012-1234567', // Assuming you want to include phone_no
            ],
            [
                'name' => 'Abdul',
                'username' => 'abdul',
                'email' => 'abdul@gmail.com',
                'password' => Hash::make('password123'), // Hashed password
                'role' => 'officer',
                'phone_no' => '013-2345678',
            ],
            // Add more officer records as needed
        ];

        foreach ($officers as $officerData) {
            User::create([
                'name' => $officerData['name'],
                'username' => $officerData['username'],
                'email' => $officerData['email'],
                'password' => $officerData['password'],
                'role' => $officerData['role'],
                'phone_no' => $officerData['phone_no'], // Insert phone_no if needed
            ]);
        }
    }
}
