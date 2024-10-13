<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ComplaintCleaner; // Import ComplaintCleaner
use App\Models\Officer;          // Import Officer model
use App\Models\Cleaner;          // Import Cleaner model
use App\Models\Task;             // Import Task model

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints'; // Specify the table name
    protected $primaryKey = 'comp_id'; // Set the primary key to comp_id
    public $incrementing = false; // Set to false if the primary key is not auto-incrementing
    protected $keyType = 'string'; // Specify the key type if it's not an integer

    // Allow mass assignment on these fields
    protected $fillable = [
        'comp_date',
        'comp_time',
        'comp_desc',
        'comp_location',
        'comp_status',
        'comp_image',
        'officer_id',
    ];

    // Define status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on going';
    const STATUS_COMPLETED = 'completed';

    // Method to retrieve available statuses
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ON_GOING,
            self::STATUS_COMPLETED,
        ];
    }

    public function showDashboard()
    {
        $recentComplaint = self::orderBy('created_at', 'desc')->first(); // Adjust if needed based on your timestamp field
        return view('dashboard', compact('recentComplaint'));
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

    // Define a relationship: one complaint has many tasks
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
}
