<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cleaner;
use App\Models\Task;
use App\Models\User; // For officer and supervisor roles
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Complaint extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'complaints'; // Specify the table name
    protected $primaryKey = 'id'; // Primary key (auto-increment)
    public $incrementing = true; // Ensure it's auto-incrementing
    protected $keyType = 'int'; // Primary key type

    // Allow mass assignment on these fields
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
        'comp_image', // Add comp_image to fillable fields
    ];

    // Define status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on going';
    const STATUS_COMPLETED = 'completed';

    /**
     * Retrieve available statuses.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ON_GOING,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Register media collections for the complaint images.
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
        // Check if there's media associated with 'complaint_images'
        if ($this->hasMedia('complaint_images')) {
            return $this->getFirstMediaUrl('complaint_images');
        }

        // Fall back to stored value or default image
        return $this->attributes['comp_image'] ?: asset('default-image.png');
    }

    /**
     * Retrieve all complaint images URLs.
     *
     * @return array
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
     * Define relationship with Officer (User model with officer role).
     */
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id')->where('role', 'officer');
    }

    /**
     * Define relationship with Supervisor (user who assigned the cleaners).
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'assigned_by')->where('role', 'supervisor');
    }

    /**
     * Define many-to-many relationship with Cleaner model.
     */
    public function cleaners()
    {
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner')
            ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
            ->withTimestamps();
    }

    /**
     * Define one-to-many relationship with Task model.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'comp_id');
    }

    /**
     * Update the status of the complaint.
     * Throws an exception if the status is invalid.
     *
     * @param string $status
     * @throws \Exception
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
     * Assign cleaners to the complaint and update relevant details.
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