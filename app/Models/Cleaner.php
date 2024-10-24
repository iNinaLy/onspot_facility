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
        'status', // You may track if the cleaner is available/busy/etc.
        'user_id', // Assuming each cleaner has a corresponding user profile
    ];

    /**
     * Define many-to-many relationship with Complaint model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner')
            ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
            ->withTimestamps();
    }

    /**
     * Define relationship with Task model (if cleaners have specific tasks)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected $table = 'cleaners';
}