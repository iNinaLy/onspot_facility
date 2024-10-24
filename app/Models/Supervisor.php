<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;
    protected $table = 'supervisors'; // Your table name
    protected $primaryKey = 'id'; // Assuming `id` is the primary key, otherwise use 's_id'
    public $timestamps = true; // For `created_at` and `updated_at`

    protected $fillable = [
        's_email',
        's_pass',
        's_name',
        's_phoneNo',
        's_username',
        'profile_pic',
        'building'
    ];

    public function cleaners()
    {
        return $this->hasMany(Cleaner::class);
    }
}
