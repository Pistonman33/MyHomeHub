<?php

namespace App\Livewire\Frontend\Ctt;

use Livewire\Component;
use App\Models\CttPlayer;
use App\Models\CttMatch;
use App\Models\CttSeason;
use App\Models\CttPlayerPointsHistory;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $season = 'all';
    public $player = '167818';

    public function baseQuery()
    {
        $query = CttMatch::query();
        $query->where('player_license', '167818');
        if ($this->season !== 'all') {
            $query->where('season_year', $this->season);
        }

        $query->where('player_license', $this->player);

        return $query;
    }

    public function getStats()
    {
        $query = $this->baseQuery();

        $wins = (clone $query)->where('result', 'V')->count();
        $losses = (clone $query)->where('result', 'D')->count();

        $total = $wins + $losses;

        return [
            'wins' => $wins,
            'losses' => $losses,
            'total' => $total,
            'percentage' => $total ? round($wins/$total*100) : 0
        ];
    }
    public function getSeasonInfo()
    {
        $s = $this->season;
        if (!$s || $s === 'all')
            $s = date('Y');
        return CttSeason::where('year', $s)->first();
    }

    public function getRankingStats()
    {
        return $this->baseQuery()
            ->select(
                'opponent_ranking',
                DB::raw("SUM(result='V') as wins"),
                DB::raw("SUM(result='D') as losses")
            )
            ->groupBy('opponent_ranking')
            ->orderBy('opponent_ranking')
            ->get();
    }

    public function getLastMatches()
    {
        return $this->baseQuery()
            ->orderByDesc('date')
            ->get()
            ->groupBy(function ($match) {
                return $match->date . '-' . $match->opponent_club;
            });
    }

    public function getSeasons()
    {
        return CttMatch::select('season_year')
            ->where('player_license', $this->player)
            ->distinct()
            ->orderByDesc('season_year')
            ->pluck('season_year');
    }

    public function getPlayers()
    {
        return CttPlayer::select('license', 'firstname', 'lastname')
            ->get();
    }

    public function updatedSeason()
    {
        $this->dispatch('refreshCharts', [
            'wins' => $this->getStats()['wins'],
            'losses' => $this->getStats()['losses']
        ]);
    }
    
    public function getTop3Opponents()
    {
      return CttPlayerPointsHistory::query()
            ->select(
                'ctt_matches.opponent_firstname',
                'ctt_matches.opponent_lastname',
                'ctt_player_points_history.delta_points',
                'ctt_player_points_history.opponent_points',
                'ctt_matches.opponent_ranking',
                'ctt_matches.opponent_club'

            )
            ->join(
                'ctt_matches',
                'ctt_player_points_history.match_id',
                '=',
                'ctt_matches.id'
            )
            ->orderByDesc('ctt_player_points_history.delta_points')
            ->limit(3)
            ->get();
    }

    public function getFlop3Opponents()
    {
      return CttPlayerPointsHistory::query()
            ->select(
                'ctt_matches.opponent_firstname',
                'ctt_matches.opponent_lastname',
                'ctt_player_points_history.delta_points',
                'ctt_player_points_history.opponent_points',
                'ctt_matches.opponent_ranking',
                'ctt_matches.opponent_club'
            )
            ->join(
                'ctt_matches',
                'ctt_player_points_history.match_id',
                '=',
                'ctt_matches.id'
            )
            ->orderBy('ctt_player_points_history.delta_points')
            ->limit(3)
            ->get();    
    }

    public function getTopOpponents()
    {
        $query = \App\Models\CttMatch::selectRaw('
                opponent_license,
                opponent_firstname,
                opponent_lastname,
                opponent_club,
                COUNT(*) as total_matches,
                SUM(CASE WHEN result IN ("W","V") THEN 1 ELSE 0 END) as wins,
                SUM(CASE WHEN result IN ("L","D") THEN 1 ELSE 0 END) as losses,
                MAX(opponent_ranking) as last_ranking
            ')
            ->groupBy('opponent_license', 'opponent_firstname', 'opponent_lastname', 'opponent_club')
            ->orderByDesc('total_matches')
            ->limit(10);

        if ($this->season !== 'all') {
            $query->where('season_year', $this->season);
        }

        return $query->get();
    }    

    public function render()
    {
        return view('livewire.frontend.ctt.dashboard', [
            'stats' => $this->getStats(),
            'rankingStats' => $this->getRankingStats(),
            'matchesGrouped' => $this->getLastMatches(),
            'seasons' => $this->getSeasons(),
            'currentRanking' => $this->getCurrentRanking(),
        ]);    
    }
}