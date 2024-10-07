<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintCleaner extends Model
{
    use HasFactory;

    protected $table = 'complaint_cleaner';

    protected $fillable = [
        'id',
        'cleaner_id',
        'no_of_cleaners',
        'assigned_by',
        'assigned_date',
    ];

    // Define relationships
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'id');
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
