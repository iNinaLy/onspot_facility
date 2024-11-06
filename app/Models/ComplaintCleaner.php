<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintCleaner extends Model
{
    use HasFactory;

    protected $table = 'complaint_cleaner'; // Explicitly set the table name
    
    protected $fillable = [
        'complaint_id',
        'cleaner_id',
        'no_of_cleaners',
        'assigned_by',
        'assigned_date',
    ];

    // Define relationship to Complaint model
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    // Define relationship to Cleaner model
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id');
    }

    // Define relationship to the user who assigned the complaint
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
