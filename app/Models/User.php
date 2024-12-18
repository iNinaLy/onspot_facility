<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

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

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Automatically delete associated media when user is deleted
            $user->clearMediaCollection('profile_pictures');
        });
    }

    /**
     * Register media collection for profile pictures.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile() // Only allow one profile picture at a time
             ->useDisk('public'); // Use 'public' disk
    }

    /**
     * Accessor for profile picture URL.
     */
    public function getProfilePicAttribute($value)
    {
        return $value ?: asset('storage/profile_pic/default.webp');
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

    public function cleaner()
    {
        return $this->hasOne(Cleaner::class, 'user_id');
    }

}
