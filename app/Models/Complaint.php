<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ComplaintCleaner; // Import ComplaintCleaner

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

    public function showDashboard()
    {
        $recentComplaint = Complaint::orderBy('created_at', 'desc')->first(); // Adjust if needed based on your timestamp field
        return view('dashboard', compact('recentComplaint'));
    }
    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id'); // Replace 'officer_id' with the actual foreign key if different
    }

    public function cleaners()
    {
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner', 'comp_id', 'cleaner_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }


    public function cleanerAssignments()
    {
        return $this->hasMany(ComplaintCleaner::class, 'comp_id');
    }
    
    // Define a relationship: one complaint has many tasks
    public function tasks()
    {
        return $this->hasMany(Task::class, 'comp_id', 'comp_id'); 
        // 'comp_id' is the foreign key in tasks table, 'comp_id' is the primary key in complaints table
    }


    // In Complaint.php model

    public function updateStatus($status)
    {
        if (in_array($status, ['Pending', 'Notified', 'Ongoing', 'Completed'])) {
            $this->comp_status = $status;
            $this->save();
        } else {
            throw new \Exception("Invalid status");
        }
    }

}

