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
        'password'          => 'hashed',
    ];

    /**
     * Relationship with the Complaint model.
     */
    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'cleaner_id', 'complaint_id')
                    ->withPivot('assigned_by', 'assigned_date', 'no_of_cleaners')
                    ->withTimestamps();
    }

    /**
     * Relationship with NotificationToken model.
     */
    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class, 'user_id');
    }

    /**
     * Register media collections for the user's profile picture.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile()
             ->useDisk('public');
    }

    /**
     * Accessor for the profile picture URL.
     */
    public function getProfilePicAttribute($value)
    {
        return $this->getFirstMediaUrl('profile_pictures') ?: asset('default-profile.png');
    }

    /**
     * Scope to check if the user is a cleaner.
     */
    public function scopeIsCleaner($query)
    {
        return $query->where('role', 'cleaner');
    }

    /**
     * Scope to check if the user is a supervisor.
     */
    public function scopeIsSupervisor($query)
    {
        return $query->where('role', 'supervisor');
    }

    /**
     * Scope to check if the user is an officer.
     */
    public function scopeIsOfficer($query)
    {
        return $query->where('role', 'officer');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications->markAsRead();
    }
}
