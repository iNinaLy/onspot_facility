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
        'user_id', // Assuming each cleaner has a corresponding user profile
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
            ->withTimestamps();
    }

    /**
     * Define relationship with Task model (if cleaners have specific tasks)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Register media collections for the Cleaner (for profile pictures)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->useDisk('public'); // Use the 'public' disk for storage
    }

    protected $table = 'cleaners';
}