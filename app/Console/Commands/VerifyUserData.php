<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class VerifyUserData extends Command
{
    protected $signature = 'verify:user-data';
    protected $description = 'Verify and update user data integrity';

    public function handle()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Check if username or phone_no are null or incorrect
            if (is_null($user->username) || empty($user->username)) {
                $this->info("User {$user->id} has no username.");
                // Logic to update username if needed
            }
            if (is_null($user->phone_no) || empty($user->phone_no)) {
                $this->info("User {$user->id} has no phone number.");
                // Logic to update phone_no if needed
            }
        }
    }
}


