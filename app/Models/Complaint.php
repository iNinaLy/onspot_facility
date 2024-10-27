<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ComplaintCleaner;
use App\Models\Officer;
use App\Models\Cleaner;
use App\Models\Task;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Complaint extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'complaints';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'comp_date',
        'comp_time',
        'comp_desc',
        'comp_location',
        'comp_status',
        'officer_id',
        'assigned_by',
        'assigned_date',
        'no_of_cleaners',
        'cleaner_id',
        'comp_image', // Add comp_image to fillable fields
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on going';
    const STATUS_COMPLETED = 'completed';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ON_GOING,
            self::STATUS_COMPLETED,
        ];
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    public function cleaners()
    {
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner', 'comp_id', 'cleaner_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'comp_id');
    }

    /**
     * Register media collections for the complaint.
     * Ensure we're using the 'public' disk so images are accessible via URL.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('complaint_images')
             ->useDisk('public'); // Explicitly use the public disk
    }

    /**
     * Accessor for the first complaint image URL.
     * Returns a default image if no media exists for this complaint.
     */
    public function getCompImageAttribute()
    {
        return $this->attributes['comp_image'] ?: asset('default-image.png');
    }

    /**
     * Retrieve all complaint images.
     */
    public function getAllImagesUrlsAttribute()
    {
        $mediaItems = $this->getMedia('complaint_images');
        $imageUrls = [];
    
        foreach ($mediaItems as $media) {
            $imageUrls[] = $media->getUrl(); // Collect URLs of all images
        }
    
        return $imageUrls; // Return an array of image URLs
    }

    /**
     * Update complaint status.
     */
    public function updateStatus($status)
    {
        if (in_array($status, self::getStatuses())) {
            $this->comp_status = $status;
            $this->save();
        } else {
            throw new \Exception("Invalid status: $status");
        }
    }
}