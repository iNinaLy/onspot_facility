<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use App\Models\ComplaintCleaner;
use App\Models\Officer;
use App\Models\Cleaner;
use App\Models\Task;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
=======
use Illuminate\Support\Facades\Storage;
>>>>>>> origin/of

class Complaint extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

<<<<<<< HEAD
    protected $table = 'complaints';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
=======
    protected $table = 'complaints'; // Specify the table name
    protected $primaryKey = 'id'; // Primary key (auto-increment)
>>>>>>> origin/of

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
<<<<<<< HEAD
        'cleaner_id',
        'comp_image', // Add comp_image to fillable fields
=======
>>>>>>> origin/of
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on going';
    const STATUS_COMPLETED = 'completed';

<<<<<<< HEAD
=======
    /**
     * Retrieve available statuses
     *
     * @return array
     */
>>>>>>> origin/of
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ON_GOING,
            self::STATUS_COMPLETED,
        ];
    }

<<<<<<< HEAD
=======
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('complaint_images')
             ->useDisk('public'); // Explicitly use the public disk
    }

    /**
     * Accessor to retrieve the binary image as a Base64-encoded string.
     * 
     * @return string|null
     */
    public function getCompImageAttribute($value)
    {
        // Check if there's media associated with 'complaint_images'
        if ($this->hasMedia('complaint_images')) {
            return $this->getFirstMediaUrl('complaint_images');
        }

        // If no media is present, fall back to the stored value or default image
        return $value ?: asset('default-image.png');
    }

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
     * Define relationship with Officer model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
>>>>>>> origin/of
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id')->where('role', 'officer');
    }

    /**
     * Define relationship with Supervisor (user who assigned the cleaners)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'assigned_by')->where('role', 'supervisor');
    }

    /**
     * Define many-to-many relationship with Cleaner model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cleaners()
    {
<<<<<<< HEAD
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner', 'comp_id', 'cleaner_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'comp_id');
=======
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner')
            ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
            ->withTimestamps();
>>>>>>> origin/of
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

    /**
     * Assign cleaners to the complaint and update relevant details
     *
     * @param array $cleanerIds
     * @param string $assignedBy
     * @param int $noOfCleaners
     * @throws \Exception
     */
    public function assignCleaners(array $cleanerIds, $assignedBy, $noOfCleaners)
    {
        // Ensure cleaners are assigned only if status is pending
        if ($this->comp_status !== self::STATUS_PENDING) {
            throw new \Exception("Cleaners can only be assigned when the complaint status is 'pending'.");
        }

        // Assign the cleaners using syncWithPivotValues method
        $this->cleaners()->syncWithPivotValues($cleanerIds, [
            'assigned_by' => $assignedBy,
            'assigned_date' => now(),
            'no_of_cleaners' => $noOfCleaners,
        ]);

        // Update the complaint status to on-going
        $this->updateStatus(self::STATUS_ON_GOING);
    }
}
