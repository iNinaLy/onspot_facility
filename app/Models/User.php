<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'username',
        'name',
        'email',
        'profile_pic', // Profile picture for web
        'password',
        'phone_no',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Register media collections for the user profile picture.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile() // Ensure only one profile picture per user
             ->useDisk('public'); // Use public disk to make the images accessible
    }

    /**
     * Accessor for the profile picture URL.
     * Returns a default image if no media exists for this user.
     */
    public function getProfilePicAttribute()
    {
        // Check if there's a media item in the profile_pictures collection
        if ($this->hasMedia('profile_pictures')) {
            return $this->getFirstMediaUrl('profile_pictures');
        }

        // Fallback to stored value or default image if no profile picture is set
        return $this->attributes['profile_pic'] ?: asset('default-profile.png');
    }

    // Your role-checking and other methods remain the same...

    public function isCleaner()
    {
        return $this->role === 'cleaner';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isOfficer()
    {
        return $this->role === 'officer';
    }
}
