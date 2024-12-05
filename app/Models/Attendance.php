<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cleaner_id',
        'attend_date',
        'attend_in',
        'attend_status'
    ];
    

    // Define the relationship with the Cleaner model
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class, 'cleaner_id');
    }
}    