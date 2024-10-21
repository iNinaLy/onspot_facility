<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    // Allow mass assignment on these fields
    protected $fillable = [
        's_email', 's_pass', 's_name', 's_phoneNo'
    ];

    public function cleaners()
    {
        return $this->hasMany(Cleaner::class);
    }

}