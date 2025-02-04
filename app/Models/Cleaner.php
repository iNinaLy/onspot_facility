<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Cleaner extends Authenticatable implements HasMedia
{
    use HasFactory, InteractsWithMedia, Notifiable;

    protected $table = 'cleaners';
    protected $primaryKey = 'id';  
    public $incrementing = true;   
    protected $keyType = 'int';   
    
    protected $fillable = [
        'user_id',
        'cleaner_name',
        'cleaner_phoneNo',
        'profile_pic',
        'cleaner_username',
        'cleaner_password',
        'status',
        'building',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_UNAVAILABLE = 'unavailable';

 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_cleaner', 'cleaner_id', 'complaint_id')
                    ->withPivot('no_of_cleaners', 'assigned_by', 'assigned_date')
                    ->withTimestamps();
    }


    public function setCleanerPasswordAttribute($value)
    {
        $this->attributes['cleaner_password'] = Hash::make($value);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }


    public function ongoingComplaints()
    {
        return $this->hasMany(Complaint::class, 'cleaner_id')->where('comp_status', 'ongoing');
    }

    public function scopeWithOngoingComplaints($query)
    {
        return $query->where('status', self::STATUS_UNAVAILABLE)
                     ->whereHas('complaints', function ($q) {
                         $q->where('comp_status', Complaint::STATUS_ONGOING);
                     })
                     ->with(['complaints' => function ($q) {
                         $q->where('comp_status', Complaint::STATUS_ONGOING);
                     }]);
    }


    public function getOngoingComplaintsAttribute()
    {
        return $this->complaints()->where('comp_status', Complaint::STATUS_ONGOING)->get();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
             ->singleFile()
             ->useDisk('public');
    }

    public function getProfilePictureUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('profile_pictures') ?: asset('default-cleaner.png');
    }

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class, 'user_id');
    }
}
