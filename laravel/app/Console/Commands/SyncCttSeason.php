<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CttSyncService;
use App\Models\CttPlayer;

class SyncCttSeason extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctt:sync {license?} {year?} {--year=}'; // php artisan ctt:sync 167818 2026

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
        $year = $this->option('year') ?: $this->argument('year') ?: (int) date('Y');

        $this->info('Starting CTTSync for license: ' . ($license ?? 'all') . ' and year: ' . $year);

        $service = app(CttSyncService::class);

        if ($license) {
            $this->syncLicense($service, $license, $year);
            return 0;
        }

        $players = CttPlayer::all();
        if ($players->isEmpty()) {
            $this->warn('No CTT players defined in database.');
            return 0;
        }

        foreach ($players as $player) {
            $this->syncLicense($service, $player->license, $year);
        }

        return 0;
    }

    protected function syncLicense(CttSyncService $service, int $license, int $year): void
    {
        try {
            $this->info($service->sync($license, $year));
        } catch (\Exception $e) {
            $this->error('Sync failed for license ' . $license . ': ' . $e->getMessage());
        }
    }

}