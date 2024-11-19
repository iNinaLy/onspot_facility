<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification; // Fix for DatabaseNotification
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasRoles;

    protected $fillable = [
        'username',
        'name',
        'email',
        'profile_pic', // Profile picture for web
        'password',
        'phone_no',
        'role', // Ensure this is included in the fillable array
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
        if ($this->hasMedia('profile_pictures')) {
            return $this->getFirstMediaUrl('profile_pictures');
        }

        return $this->attributes['profile_pic'] ?: asset('default-profile.png');
    }

    /**
     * Check if the user is a cleaner.
     */
    public function isCleaner(): bool
    {
        return $this->attributes['role'] === 'cleaner'; // Access role via attributes
    }

    /**
     * Check if the user is a supervisor.
     */
    public function isSupervisor(): bool
    {
        return $this->attributes['role'] === 'supervisor'; // Access role via attributes
    }

    /**
     * Check if the user is an officer.
     */
    public function isOfficer(): bool
    {
        return $this->attributes['role'] === 'officer'; // Access role via attributes
    }

    /**
     * Relationship with NotificationToken model.
     */
    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    /**
     * Retrieve all notifications for the user.
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable');
    }
    

    /**
     * Retrieve only unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
    }
}
