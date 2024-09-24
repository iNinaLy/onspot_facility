<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cleaner extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'cleaner_id',
        'cleaner_name', 
        'cleaner_phoneNo', 
        'cleaner_available'
    ];
}
