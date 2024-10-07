<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define the primary key
    protected $primaryKey = 'task_id';

    // Allow mass assignment for these fields
    protected $fillable = ['no_of_cleaner', 'comp_id', 's_id'];

    // Optionally, if your primary key is not auto-incrementing, add this:
    public $incrementing = false;

    // If you need to disable timestamps for the model, add this:
    public $timestamps = true;

    protected $table = 'tasks'; // Specify your tasks table name

    // Define a relationship: many tasks belong to one complaint
    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'comp_id', 'comp_id'); 
        // 'comp_id' is the foreign key in tasks, 'comp_id' is the primary key in complaints
    }
}


