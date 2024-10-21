<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'username', 
        'email',
        'password',
        'phone_no', 
        'profile_pic', 
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isCleaner()
    {
        return $this->role === 'cleaner';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isOfficer()
    {
        return $this->role === 'officer';
    }
}
