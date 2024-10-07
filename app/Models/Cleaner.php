<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cleaner extends Model
{
   

    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'cleaner_id',
        'cleaner_name', 
        'cleaner_phoneNo', 
        'cleaner_available'
    ];

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'cleaner_id', 'comp_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_cleaner', 'cleaner_id', 'task_id');
    }

    protected $table = 'cleaners';
}
