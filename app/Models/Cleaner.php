<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cleaner extends Model
{
   

    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'cleaner_username',
        'cleaner_name',
        'cleaner_phoneNo',
        'profile_pic',
        'status',
        'user_id',
    ];
    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'cleaner_id', 'comp_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }
  
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }


    protected $table = 'cleaners';
}
