<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property DatabaseNotificationCollection|DatabaseNotification[] $unreadNotifications
 * @property DatabaseNotificationCollection|DatabaseNotification[] $readNotifications
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany notifications()
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany readNotifications()
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany unreadNotifications()
 */
class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasRoles;

    protected $fillable = [
        'username',
        'name',
        'email',
        'profile_pic',
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
    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'cleaner_complaint', 'cleaner_id', 'complaint_id')
                    ->withPivot('assigned_by', 'assigned_date', 'no_of_cleaners')
                    ->withTimestamps();
    }

    /**
     * Register media collections for the user profile picture.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile()
             ->useDisk('public');
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
        return $this->role === 'cleaner';
    }

    /**
     * Check if the user is a supervisor.
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if the user is an officer.
     */
    public function isOfficer(): bool
    {
        return $this->role === 'officer';
    }

    /**
     * Relationship with NotificationToken model.
     */
    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class, 'user_id');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications->markAsRead();
    }
}
