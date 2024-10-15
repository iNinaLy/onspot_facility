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
<<<<<<< HEAD
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'id', 'comp_id');
=======
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'cleaner_id', 'comp_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
>>>>>>> origin/of
    }
  
    public function tasks()
    {
<<<<<<< HEAD
        return $this->belongsToMany(Task::class, 'task_cleaner', 'id', 'task_id');
=======
        return $this->hasMany(Task::class);
>>>>>>> origin/of
    }


    protected $table = 'cleaners';
}
