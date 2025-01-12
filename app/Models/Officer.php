<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $table = 'officers';

    // Fields that are mass assignable
    protected $fillable = [
        'officer_email', 
        'officer_pass', 
        'officer_name', 
        'officer_phoneNo',
        'officer_username', // Added based on your schema
        'profile_pic',
    ];

    // Define the relationship with the User model if applicable
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Ensure this is correct if you have a user_id field
    }

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    // Ensure the officer has a role attribute
    protected $attributes = [
        'role' => 'officer',
    ];
}