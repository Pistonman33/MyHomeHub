<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CttSyncService;

class SyncCttSeason extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctt:sync {license?} {year?}'; // php artisan ctt:sync 167818 2026

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize a full season from the TabT/VTTL API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $license = $this->argument('license');
        $year = $this->argument('year');

        $this->info('Starting CTTSync for license: ' . ($license ?? 'all') . ' and year: ' . ($year ?? 'all'));

        $service = app(CttSyncService::class);

        try {
            $this->info($service->sync($license,$year));
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
        }
    }
}