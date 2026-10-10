<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\AfttClient;
use App\Services\AfttParser;
use App\Models\CttPlayer;
use App\Models\CttSeason;
use App\Models\CttMatch;
use App\Models\CttPlayerPointsHistory;
use App\Models\CttPlayerSeason;
use Carbon\Carbon;

class SyncAfttResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctt:aftt_parse {license?}'; // php artisan ctt:aftt_parse {license}

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse the DATA AFTT results for the current season and update the database';

    /**
     * Execute the console command.
     */
    public function handle(AfttClient $client, AfttParser $parser)
    {
        $this->info('Starting AFTT parsing...');
        $license = $this->argument('license');

        if ($license) {
            return $this->processLicense($license, $client, $parser);
        }

        $players = CttPlayer::all();
        if ($players->isEmpty()) {
            $this->error('No CTT players defined in database.');
            return 1;
        }

        foreach ($players as $player) {
            $this->line('Parsing AFTT data for license ' . $player->license);
            $this->processLicense($player->license, $client, $parser);
        }

        return 0;
    }

    protected function processLicense(int $license, AfttClient $client, AfttParser $parser): int
    {
        $player = CttPlayer::find($license);
        if (! $player || ! $player->password) {
            $this->error("Player with license $license not found or password is missing.");
            return 1;
        }

        $client->setCredentials($license, $player->decrypted_password);

        // 1. login
        $session = $client->login();

        // 2. fetch page
        $html = $session['dashboard'] ?? $client->getDashboard($session['cookies'], $license);

        // 3. parse general information (points, ranking, etc.)
        $data = $parser->parseDashboard($html);

        $seasonYear = $data['season_year'] ?? CttSeason::currentSeasonYear();
        $season = CttSeason::find($seasonYear);
        if (!$season) {
            $this->error("Season {$seasonYear} is not in the database. Run ctt:sync {$license} {$seasonYear} first.");
            return 1;
        }

        if ($data['starting_points'] === null || $data['recent_points'] === null) {
            $this->error('AFTT page did not provide both starting and current points; no season snapshot was saved.');
            return 1;
        }

        $playerSeason = CttPlayerSeason::firstOrCreate([
            'year' => $seasonYear,
            'player_license' => $license,
        ]);

        $this->info("Season AFTT détectée: {$season->name} ({$seasonYear})");
        $this->info('Points départs: ' . $data['starting_points']);
        $this->info('Points actuels: ' . $data['recent_points']);
        $this->info('Ranking: ' . $data['ranking_belgium']);

        // 4. parse matches
        $matches = $parser->parseMatches($html);

        $savedHistoryRows = 0;
        foreach ($matches as $match) {
            if (!$match['date'] || !$match['match_id']) {
                continue;
            }

            $matchDate = Carbon::createFromFormat('d/m/Y', $match['date'])->format('Y-m-d');

            foreach ($match['players'] as $player) {
                if ($player['delta'] === null || $player['opponent_points'] === null) {
                    continue;
                }

                $matchRow = CttMatch::where('match_id', $match['match_id'])
                        ->whereDate('date', $matchDate)
                        ->where('season_year', $seasonYear)
                        ->where('player_license', $license)
                        ->whereRaw("LOWER(CONCAT(opponent_firstname, ' ', opponent_lastname)) = ?", [
                            strtolower(trim($player['name']))
                        ])
                        ->first();
                if (!$matchRow) {
                    $this->error("Match {$match['match_id']} ({$matchDate}) vs {$player['name']} not found in season {$seasonYear}. Run ctt:sync {$license} {$seasonYear} first.");
                    return 1;
                }

                CttPlayerPointsHistory::updateOrCreate(
                    ['match_id' => $matchRow->id, 'player_license' => $license],
                    [
                        'delta_points' => $player['delta'],
                        'opponent_points' => $player['opponent_points'],
                    ]
                );
                $savedHistoryRows++;
            }
        }

        $playerSeason->fill([
            'starting_points' => $data['starting_points'],
            'current_points' => $data['recent_points'],
            'ranking_belgium' => $data['ranking_belgium'],
        ])->save();

        $this->info("Historique AFTT enregistré: {$savedHistoryRows} adversaire(s).");
        
        $this->info('Calculating points history...');

        $matchesForSeason = $season->matches()
            ->where('player_license', $license)
            ->whereDate('date', '<', now()->startOfMonth()->toDateString())
            ->with(['pointsHistory' => function ($query) use ($license) {
                $query->where('player_license', $license);
            }])
            ->get();

        $points_in_season = (float) $playerSeason->starting_points + $matchesForSeason->sum(function ($match) {
            return (float) optional($match->pointsHistory->first())->delta_points;
        });

        $pendingMatches = $season->matches()
            ->where('player_license', $license)
            ->whereDate('date', '>=', now()->startOfMonth()->toDateString())
            ->with(['pointsHistory' => function ($query) use ($license) {
                $query->where('player_license', $license);
            }])
            ->get();

        $pendingPoints = $pendingMatches->sum(function ($match) {
            return (float) optional($match->pointsHistory->first())->delta_points;
        });

        if ($pendingPoints != 0.0) {
            $this->info('Points en attente de publication (matchs du mois en cours): ' . number_format($pendingPoints, 2, '.', ''));
        }

        if (round($points_in_season, 2) != round((float) $playerSeason->current_points, 2)) {
            $this->warn("Points mismatch for season {$seasonYear}: calculated " . number_format($points_in_season, 2, '.', '') . ', but current points is ' . $playerSeason->current_points);
        } else {
            $this->info("Points match: calculated $points_in_season, current points is {$playerSeason->current_points}");
        }

        $this->info('Ending AFTT parsing...');

        return 0;
    }
}