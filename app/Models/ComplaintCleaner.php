<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintCleaner extends Model
{
    use HasFactory;

    protected $table = 'complaint_cleaner';

    protected $fillable = [
        'complaint_id',
        'cleaner_id',
        'no_of_cleaners',
        'assigned_by',
        'assigned_date',
    ];

    // Define relationships
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    // Relationship with Cleaner
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id', 'user_id'); // cleaner_id references user_id in cleaners
    }
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
