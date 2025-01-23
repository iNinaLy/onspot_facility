<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Cleaner extends Authenticatable implements HasMedia
{
    use HasFactory, InteractsWithMedia, Notifiable;

    // Define the fillable fields
    protected $table = 'cleaners';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
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

    const STATUS_AVAILABLE = 'available';
    const STATUS_UNAVAILABLE = 'unavailable';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'cleaner_id', 'complaint_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }

    /**
     * Mutator to hash the cleaner's password.
     */
    public function setCleanerPasswordAttribute($value)
    {
        $this->attributes['cleaner_password'] = Hash::make($value);
    }

    /**
     * Scope to retrieve available cleaners.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }


    public function ongoingComplaints()
    {
        return $this->hasMany(Complaint::class, 'cleaner_id')->where('comp_status', 'ongoing');
    }

        /**
     * Scope to retrieve cleaners with ongoing complaints.
     */
    public function scopeWithOngoingComplaints($query)
    {
        return $query->where('status', self::STATUS_UNAVAILABLE)
                     ->whereHas('complaints', function ($q) {
                         $q->where('comp_status', Complaint::STATUS_ONGOING);
                     })
                     ->with(['complaints' => function ($q) {
                         $q->where('comp_status', Complaint::STATUS_ONGOING);
                     }]);
    }

        /**
     * Accessor to get ongoing complaints.
     */
    public function getOngoingComplaintsAttribute()
    {
        return $this->complaints()->where('comp_status', Complaint::STATUS_ONGOING)->get();
    }

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