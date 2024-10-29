<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',  // Add username
        'name',
        'email',
        'profile_pic', // Profile picture for web
        'password',
        'phone_no', // Phone number for web
        'role',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Role checking methods
     */
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

    /**
     * Method to fetch and categorize cleaners (for web usage)
     */
    public function showCleaners()
    {
        // Fetch users with the role 'cleaner'
        $cleaners = User::where('role', 'cleaner')->get();

        // Count total cleaners and categorize them by availability
        $totalCleaners = $cleaners->count();
        $availableCount = $cleaners->where('status', 'available')->count();
        $unavailableCount = $totalCleaners - $availableCount;

        // Return the view with the cleaner data (web functionality)
        return view('cleaners.index', compact('cleaners', 'totalCleaners', 'availableCount', 'unavailableCount'));
    }

    /**
     * Relationship with Officer model (for web usage)
     */
    public function officer()
    {
        return $this->hasOne(Officer::class, 'user_id');
    }
}
