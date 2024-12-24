<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\DB;

class Complaint extends Model implements HasMedia
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasFactory, InteractsWithMedia;

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
        'comp_image',
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'comp_date'      => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';

    /**
     * Retrieve available statuses.
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ONGOING,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Register media collections for complaint images.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('complaint_images')
             ->useDisk('public');
    }

    /**
     * Accessor for the first complaint image URL.
     */
    public function getCompImageAttribute()
    {
        return $this->getFirstMediaUrl('complaint_images') ?: asset('default-image.png');
    }

    /**
     * Accessor for all complaint images URLs.
     */
    public function getAllImagesUrlsAttribute()
    {
        return $this->getMedia('complaint_images')->map->getUrl()->toArray();
    }

    /**
     * Relationship with the Supervisor (User who assigned cleaners).
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'assigned_by')->where('role', 'supervisor');
    }

    /**
     * Relationship with the Officer (User handling the complaint).
     */
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Relationship with the User model.
     * Note: This method seems redundant as 'officer()' already defines the relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Many-to-many relationship with the Cleaner model.
     */
    public function cleaners()
    {
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner', 'complaint_id', 'cleaner_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }

    /**
     * Update the status of the complaint.
     *
     * @param string $status
     * @throws \Exception
     */
    public function updateStatus(string $status)
    {
        if (in_array($status, self::getStatuses())) {
            $this->comp_status = $status;
            $this->save();
        } else {
            throw new \Exception("Invalid status: {$status}");
        }
    }

    /**
     * Assign cleaners to the complaint and update relevant details.
     *
     * @param array $cleanerIds
     * @param int $assignedBy
     * @param int $noOfCleaners
     * @throws \Exception
     */
    public function assignCleaners(array $cleanerIds, int $assignedBy, int $noOfCleaners)
    {
        if ($this->comp_status !== self::STATUS_PENDING) {
            throw new \Exception("Cleaners can only be assigned when the complaint status is 'pending'.");
        }

        // Begin a database transaction to ensure data integrity
        DB::transaction(function () use ($cleanerIds, $assignedBy, $noOfCleaners) {
            // Attach cleaners with pivot data
            $this->cleaners()->syncWithPivotValues($cleanerIds, [
                'assigned_by'    => $assignedBy,
                'assigned_date'  => now(),
                'no_of_cleaners' => $noOfCleaners,
            ]);

            // Update the complaint's status and assignment details
            $this->update([
                'comp_status'    => self::STATUS_ONGOING,
                'assigned_by'    => $assignedBy,
                'assigned_date'  => now(),
                'no_of_cleaners' => $noOfCleaners,
            ]);

            // Update each cleaner's status to 'unavailable'
            Cleaner::whereIn('user_id', $cleanerIds)
                ->update(['status' => Cleaner::STATUS_UNAVAILABLE]);
        });
    }

}
