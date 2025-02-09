<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintCleaner extends Model
{
    protected $table = 'complaint_cleaner';

    protected $fillable = [
        'complaint_id',
        'cleaner_id',
        'no_of_cleaners',
        'assigned_by',
        'assigned_date',
        'is_notified',
    ];

    

    public $timestamps = false; // or true if you have created_at / updated_at

    // If you want relationships:
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id');
    }
}
