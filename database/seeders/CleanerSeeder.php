<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use App\Models\Cleaner; // Make sure to import your Cleaner model
use Illuminate\Support\Facades\Hash;

class CleanerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create sample cleaner users and insert into both users and cleaners tables
        $cleaners = [
            [
                'cleaner_name' => 'Ain Amira',
                'cleaner_phoneNo' => '012-1234567',
                'username' => 'ainamira',
                'email' => 'ainamira1@example.com',
                'password' => Hash::make('password123'),
                'status' => 'available',
            ],
            [
                'cleaner_name' => 'Dani Adam',
                'cleaner_phoneNo' => '013-2345678',
                'username' => 'adamdani',
                'email' => 'daniadam@example.com',
                'password' => Hash::make('password123'),
                'status' => 'available',
            ],
            [
                'cleaner_name' => 'Iman Fikri',
                'cleaner_phoneNo' => '014-3456789',
                'username' => 'fikriiman',
                'email' => 'fikriiman@example.com',
                'password' => Hash::make('password123'),
                'status' => 'unavailable',
            ],
            // Add more cleaner records as needed
        ];

        foreach ($cleaners as $cleanerData) {
            // Insert into users table
            $user = User::create([
                'name' => $cleanerData['cleaner_name'],
                'username' => $cleanerData['username'],
                'email' => $cleanerData['email'], // You can choose to remove this if you only use usernames
                'password' => $cleanerData['password'],
                'role' => 'cleaner', // Ensure the role is set to cleaner
            ]);

            // Now create an entry in the cleaners table using the user's ID
            Cleaner::create([
                'id' => $user->id, // Foreign key relationship with users table
                'cleaner_name' => $cleanerData['cleaner_name'],
                'cleaner_phoneNo' => $cleanerData['cleaner_phoneNo'],
                'username' => $cleanerData['username'],
                'password' => $cleanerData['password'], // You may want to store the hashed version
                'status' => $cleanerData['status'],
            ]);
        }
    }
}
