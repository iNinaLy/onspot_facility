<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Cleaner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    // Define the fillable fields
    protected $fillable = [
        'cleaner_username',
        'cleaner_name',
        'cleaner_phoneNo',
        'profile_pic', // This could be stored separately if needed, or handled via media library
        'status', // You may track if the cleaner is available/busy/etc.
        'user_id',
        'cleaner_password',
    ];

    /**
     * Define many-to-many relationship with Complaint model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps()
                    ->with(['cleaners' => function ($query) {
                        $query->select('id', 'cleaner_name', 'cleaner_phoneNo'); // Load name and phone number
                    }]);
    }

    /**
     * Define relationship with Task model (if cleaners have specific tasks)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    /**
     * Register media collections for the Cleaner (for profile pictures)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile()          // Ensure only one profile picture is kept per cleaner
             ->useDisk('public');     // Store files on the 'public' disk, accessible through 'storage/app/public'
    }

    public function getProfilePictureUrlAttribute(): string
    {
        // Use the value in the database if it exists
        if (!empty($this->profile_pic) && str_contains($this->profile_pic, 'http')) {
            return $this->profile_pic;
        }
    
        // Check the media library for associated profile picture
        $mediaUrl = $this->getFirstMediaUrl('profile_pictures');
        if (!empty($mediaUrl)) {
            return $mediaUrl;
        }
    
        // Fall back to the default image
        return asset('storage/profile_pic/default.webp');
    }
     

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getProfilePicAttribute($value)
    {
        // Return the URL from the database or the default image URL
        return $value ?: asset('storage/profile_pic/default.webp');
    }

    public function setProfilePicAttribute($value)
{
    $this->attributes['profile_pic'] = $value;

    // Automatically sync profile_pic with the related user
    if ($this->user) {
        $this->user->update(['profile_pic' => $value]);
    }
}


}