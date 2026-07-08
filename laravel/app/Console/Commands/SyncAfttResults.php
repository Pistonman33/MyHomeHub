<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\AfttClient;
use App\Services\AfttParser;
use App\Models\CttPlayer;
use App\Models\CttSeason;
use App\Models\CttMatch;
use App\Models\CttPlayerPointsHistory;
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
        $html = $client->getDashboard($session['cookies']);

        // 3. parse general information (points, ranking, etc.)
        $data = $parser->parseDashboard($html);

        $season = CttSeason::firstWhere('is_current', 1);

        $playerSeason = $season->playerSeasons()->firstWhere('player_license', $license);


        $this->info('Points départs: ' . $data['starting_points']);
        $this->info('Points actuels: ' . $data['recent_points']);
        $this->info('Ranking: ' . $data['ranking_belgium']);

        $playerSeason->fill([
            'starting_points' => $data['starting_points'] ?? null,
            'current_points'  => $data['recent_points'] ?? null,
            'ranking_belgium' => $data['ranking_belgium'] ?? null,
        ]);

        $playerSeason->save();
        
        // 4. parse matches
        $matches = $parser->parseMatches($html);
        
        foreach ($matches as $match) {
            foreach ($match['players'] as $player) {

                $match_date = Carbon::createFromFormat('d/m/Y', $match['date'])->format('Y-m-d');

                $matchRow = CttMatch::where('match_id', $match['match_id'])
                        ->whereDate('date', $match_date)
                        ->where('player_license', $license)
                        ->whereRaw("LOWER(CONCAT(opponent_firstname, ' ', opponent_lastname)) = ?", [
                            strtolower(trim($player['name']))
                        ])
                        ->first();
                if (!$matchRow) {
                    $this->error("Match not found in DB !!!!");
                    exit;
                }else{
                    CttPlayerPointsHistory::updateOrCreate(
                        ['match_id' => $matchRow->id, 'player_license' => $license],
                        
                        [
                            'delta_points' => $player['delta'],
                            'opponent_points' => $player['opponent_points'],
                        ]
                    );
                }
            }
        }
        
        $this->info('Calculating points history...');

        $points_in_season = $playerSeason->starting_points + $season->matches->sum(function ($match) use ($license) {
            return optional(
                $match->pointsHistory->firstWhere('player_license', $license)
            )->delta_points ?? 0;
        });

        if (round($points_in_season, 2) != round($playerSeason->current_points, 2)) {
            $this->error("Points mismatch: calculated $points_in_season, but current points is {$playerSeason->current_points}");
        } else {
            $this->info("Points match: calculated $points_in_season, current points is {$playerSeason->current_points}");
        }

        $this->info('Ending AFTT parsing...');

        return 0;
    }
}
