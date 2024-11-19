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
        'status',    // Track cleaner availability status
        'user_id',   // Reference to the user table if each cleaner has a user profile
    ];


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
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps()
                    ->with(['cleaners' => function ($query) {
                        $query->select('id', 'cleaner_name', 'cleaner_phoneNo'); // Load name and phone number
                    }]);
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
}
