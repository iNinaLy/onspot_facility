<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;

class MarkAbsentCleaners extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Mark cleaners as absent if they have not submitted their attendance for the day';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $cleaners = User::where('role', 'cleaner')->get(); // Fetch all cleaners

        foreach ($cleaners as $cleaner) {
            $todayAttendance = Attendance::where('cleaner_id', $cleaner->id)
                ->whereDate('attend_date', now()->toDateString())
                ->first();

            if (!$todayAttendance) {
                // Mark as absent if no attendance found
                Attendance::create([
                    'cleaner_id' => $cleaner->id,
                    'attend_status' => 'absent',
                    'attend_date' => now(),
                    'attend_in' => null, // No "in time" for absentees
                ]);

                $this->info("Marked cleaner {$cleaner->name} (ID: {$cleaner->id}) as absent.");
            }
        }

        $this->info('Attendance marking process completed.');
    }
}
