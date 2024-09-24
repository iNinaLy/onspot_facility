<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'name', 
        'email', 
        'rank', 
        'phone_number'
    ];
}
