<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cleaner extends Model
{
   

    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'id',
        'cleaner_name', 
        'cleaner_phoneNo', 
        'status',
        'username',
        'password',
    ];

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'id', 'comp_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_cleaner', 'id', 'task_id');
    }

    protected $table = 'cleaners';
}
