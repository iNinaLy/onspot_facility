<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Cleaner extends Authenticatable implements HasMedia 

{
    use HasFactory, InteractsWithMedia,Notifiable;

    protected $table = 'cleaners';
    protected $primaryKey = 'user_id'; // Set this to user_id instead of id
    public $incrementing = false;      // If user_id is not an auto-increment column
    protected $keyType = 'int'; 
    
    protected $fillable = [
        'user_id',
        'cleaner_name',
        'cleaner_phoneNo',
        'profile_pic',
        'cleaner_username',
        'cleaner_password',
        'status',
        'building',
    ];

    protected $attributes = [
        'role' => 'cleaner',
    ];
    const STATUS_AVAILABLE = 'available';
    const STATUS_BUSY = 'busy';
    public function setCleanerPasswordAttribute($value)
    {
        $this->attributes['cleaner_password'] = Hash::make($value);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }


    /**
     * Many-to-many relationship with the Complaint model.
     * Cleaners can be assigned to multiple complaints.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function complaints()
    {
        return $this->belongsToMany(
            Complaint::class,
            'complaint_cleaner', 
            'cleaner_id', 
            'complaint_id'
        )
        ->withPivot('assigned_by', 'no_of_cleaners', 'assigned_date')
        ->withTimestamps();
    }


    public function scopeWithOngoingComplaints($query)
    {
        return $query->where('status', 'unavailable')
                     ->whereHas('complaints', function($q){
                         $q->where('comp_status', 'ongoing');
                     })
                     ->with(['complaints' => function($q){
                         $q->where('comp_status', 'ongoing');
                     }]);
    }

    /**
     * Get ongoing complaints
     */
    public function ongoingComplaints()
    {
        return $this->complaints()->where('comp_status', 'ongoing');
    }

    /**
     * One-to-many relationship with Task model (if cleaners have specific tasks).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    

    /**
     * Register media collections for the Cleaner.
     * This method defines a collection for storing profile pictures.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile()          // Ensure only one profile picture is kept per cleaner
             ->useDisk('public');     // Store files on the 'public' disk, accessible through 'storage/app/public'
    }

    /**
     * Accessor to get the URL of the profile picture.
     * Returns a default image if no profile picture is available.
     *
     * @return string
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('profile_pictures') ?: asset('default-profile.png');
    }

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }
}
