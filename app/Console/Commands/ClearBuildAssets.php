<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearBuildAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear build assets from the public/build or public/assets folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $buildPath = public_path('build'); // Or 'assets', depending on your setup

        if (File::exists($buildPath)) {
            File::deleteDirectory($buildPath);
            $this->info("Assets cleared successfully from {$buildPath}.");
        } else {
            $this->info("No build folder found at {$buildPath}.");
        }

        return Command::SUCCESS;
    }
}
