<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
* @property-read \Illuminate\Database\Eloquent\Collection|\Illuminate\Notifications\DatabaseNotification[] $notifications
* @property-read \Illuminate\Database\Eloquent\Collection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
* @property-read \Illuminate\Database\Eloquent\Collection|\Illuminate\Notifications\DatabaseNotification[] $readNotifications
*/
class User extends Authenticatable implements HasMedia
{
   use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasRoles;

    // Mass-assignable attributes
    protected $fillable = [
        'username',
        'name',
        'email',
        'profile_pic', // Profile picture for web
        'password',
        'phone_no',
        'role', // User role: cleaner, supervisor, or officer
        'email_verified_at',
    ];

    // Attributes to hide from arrays
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Attribute casting
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Media Collections: Profile Pictures
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile() // Only one profile picture per user
             ->useDisk('public'); // Store files on the 'public' disk
    }

    /**
     * Accessor for the profile picture URL.
     * Returns the user's profile picture or a default image if none exists.
     *
     * @return string
     */
    public function getProfilePicAttribute()
    {
        if ($this->hasMedia('profile_pictures')) {
            return $this->getFirstMediaUrl('profile_pictures');
        }

        return $this->attributes['profile_pic'] ?: asset('default-profile.png');
    }

    // Role-based Methods

    /**
     * Check if the user is a cleaner.
     *
     * @return bool
     */
    public function isCleaner(): bool
    {
        return $this->attributes['role'] === 'cleaner';
    }

    /**
     * Check if the user is a supervisor.
     *
     * @return bool
     */
    public function isSupervisor(): bool
    {
        return $this->attributes['role'] === 'supervisor';
    }

    /**
     * Check if the user is an officer.
     *
     * @return bool
     */
    public function isOfficer(): bool
    {
        return $this->attributes['role'] === 'officer';
    }

    // Notification Methods

    /**
     * Relationship with NotificationToken model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }

    /**
     * Retrieve all notifications for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable');
    }

    /**
     * Retrieve only unread notifications for the user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
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

    /**
    * Handle dynamic method calls into the model.
    *
    * This helps static analyzers recognize dynamically defined methods.
    *
    * @param string $method
    * @param array $parameters
    * @return mixed
    */
    public function __call($method, $parameters)
    {
        // Check if the method being called is related to notifications
        if (in_array($method, ['notifications', 'readNotifications', 'unreadNotifications'])) {
            return parent::__call($method, $parameters);
        }

        return parent::__call($method, $parameters);
    }
}
