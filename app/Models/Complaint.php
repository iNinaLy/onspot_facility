<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Make sure to import Carbon

class Complaint extends Model implements HasMedia
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasFactory, InteractsWithMedia;

    protected $table = 'complaints';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'comp_date',
        'comp_time',
        'comp_desc',
        'comp_location',
        'comp_status',
        'officer_id',
        'assigned_by',
        'assigned_date',
        'no_of_cleaners',
        'comp_image',
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'comp_date'      => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';

  
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ONGOING,
            self::STATUS_COMPLETED,
        ];
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('complaint_images')
             ->useDisk('public');
    }

    /**
     * Accessor for the first complaint image URL.
     */
    public function getCompImageAttribute()
    {
        return $this->getFirstMediaUrl('complaint_images') ?: asset('default-image.png');
    }

    /**
     * Accessor for all complaint images URLs.
     */
    public function getAllImagesUrlsAttribute()
    {
        return $this->getMedia('complaint_images')->map->getUrl()->toArray();
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function cleaners()
    {
        return $this->belongsToMany(Cleaner::class, 'complaint_cleaner', 'complaint_id', 'cleaner_id')
                    ->withPivot( 'assigned_by',
                    'assigned_date',
                    'no_of_cleaners',
                    'created_at',
                    'updated_at')
                    ->withTimestamps();
    }


    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function updateStatus(string $status)
    {
        if (in_array($status, self::getStatuses())) {
            $this->comp_status = $status;
            $this->save();
        } else {
            throw new \Exception("Invalid status: {$status}");
        }
    }

    public function assignCleaners(array $cleanerIds, int $supervisorId, int $noOfCleaners): void
    {

        DB::beginTransaction();

        try {
            $this->cleaners()->attach($cleanerIds, [
                'assigned_by'      => $supervisorId,
                'assigned_date'    => now(),
                'no_of_cleaners'   => $noOfCleaners,
                'is_notified'      => false, // or true based on your logic
            ]);

            Cleaner::whereIn('id', $cleanerIds)
                   ->update(['status' => Cleaner::STATUS_UNAVAILABLE]);

           
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; 
        }
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('comp_status', $status);
    }

    public function scopeAssignedToday($query)
    {
        return $query->whereDate('assigned_date', Carbon::today());
    }

    public function scopeAssignedThisWeek($query)
    {
        return $query->whereBetween('assigned_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }


    public function scopeAssignedBeforeThisWeek($query)
    {
        return $query->whereDate('assigned_date', '<', Carbon::now()->startOfWeek());
    }
}
