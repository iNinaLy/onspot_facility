<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Complaint extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia; // Implement MediaLibrary

    protected $table = 'complaints'; // Specify the table name
    protected $primaryKey = 'id'; // Primary key (auto-increment)

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
    ];

    // Define status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on going';
    const STATUS_COMPLETED = 'completed';

    /**
     * Retrieve available statuses
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
     * Register media collections for complaints.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('complaint_images')
             ->useDisk('public'); // Store images in the 'public' disk
    }

    /**
     * Accessor to retrieve the first complaint image URL or a default image if none exists.
     * 
     * @return string
     */
    public function getCompImageAttribute()
    {
        // Check if there's media associated with 'complaint_images'
        if ($this->hasMedia('complaint_images')) {
            return $this->getFirstMediaUrl('complaint_images');
        }

        // Return default image if no media is present
        return asset('default-image.png');
    }

    /**
     * Retrieve all complaint image URLs.
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
     * Define relationship with Officer model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner')
            ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
            ->withTimestamps();
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

        // Sync the cleaners with pivot data
        $this->cleaners()->syncWithPivotValues($cleanerIds, [
            'assigned_by' => $assignedBy,
            'assigned_date' => now(),
            'no_of_cleaners' => $noOfCleaners,
        ]);

        // Update the complaint status to on-going
        $this->updateStatus(self::STATUS_ON_GOING);
    }
}
