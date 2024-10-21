<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Officer; // Import Officer model
use App\Models\Cleaner; // Import Cleaner model

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints'; // Specify the table name
    protected $primaryKey = 'id'; // Primary key (auto-increment)

    // Allow mass assignment on these fields
    protected $fillable = [
        'comp_date',
        'comp_time',
        'comp_desc',
        'comp_location',
        'comp_status',
        'comp_image',
        'officer_id',
        'assigned_by', // New column for supervisor name
        'assigned_date', // The date when the complaint was assigned
        'no_of_cleaners', // Number of assigned cleaners
        'cleaner_id', // Foreign key for the cleaner in charge
    ];

    // Define status constants
    const STATUS_PENDING = 'pending';
    const STATUS_NOTIFIED = 'notified';
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
            self::STATUS_NOTIFIED,
            self::STATUS_ON_GOING,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Show dashboard with the most recent complaint
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showDashboard()
    {
        $recentComplaint = self::orderBy('created_at', 'desc')->first();
        return view('dashboard', compact('recentComplaint'));
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
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner', 'comp_id', 'cleaner_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }

    /**
     * Define relationship with Cleaner model (for assigned cleaner)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id');
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
