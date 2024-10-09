<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supervisor;

class SupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample data for supervisors
        $supervisors = [
            [
                's_email' => 'supervisor1@example.com',
                's_pass' => bcrypt('password123'), // Ensure to hash the password
                's_name' => 'Supervisor One',
                's_phoneNo' => '1234567890',
            ],
            [
                's_email' => 'supervisor2@example.com',
                's_pass' => bcrypt('password123'),
                's_name' => 'Supervisor Two',
                's_phoneNo' => '0987654321',
            ],
            [
                's_email' => 'supervisor3@example.com',
                's_pass' => bcrypt('password123'),
                's_name' => 'Supervisor Three',
                's_phoneNo' => '1122334455',
            ],
        ];

        // Insert data into the supervisors table
        foreach ($supervisors as $supervisor) {
            Supervisor::create($supervisor);
        }
    }
}
