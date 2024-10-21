<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'profile_pic',
        'password',
        'phone_no',
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


    public function showCleaners()
    {
        // Fetch users with the role 'cleaner'
        $cleaners = User::where('role', 'cleaner')->get();

        // Count total cleaners and categorize them by availability
        $totalCleaners = $cleaners->count();
        $availableCount = $cleaners->where('status', 'available')->count();
        $unavailableCount = $totalCleaners - $availableCount;

        // Return the view with the cleaner data
        return view('cleaners.index', compact('cleaners', 'totalCleaners', 'availableCount', 'unavailableCount'));
    }

    // Define the relationship with the Officer model
    public function officer()
    {
        return $this->hasOne(Officer::class, 'user_id');
    }
    
}
