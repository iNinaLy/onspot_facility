<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    protected $primaryKey = 'attend_id';

    protected $fillable = [
        'attend_date',
        'attend_in',
        'attend_status',
        'cleaner_id'
    ];
    

    // Define the relationship with Cleaner
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id');
    }
}