<?php

namespace App\Livewire\Frontend\Ctt;

use Livewire\Component;
use App\Models\CttMatch;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $season = 'all';

    public function baseQuery()
    {
        $query = CttMatch::query();

        if ($this->season !== 'all') {
            $query->where('season_year', $this->season);
        }

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
            ->limit(10)
            ->get();
    }

    public function getSeasons()
    {
        return CttMatch::select('season_year')
            ->distinct()
            ->orderByDesc('season_year')
            ->pluck('season_year');
    }

    public function render()
    {
        return view('livewire.frontend.ctt.dashboard',[
            'stats'=>$this->getStats(),
            'rankingStats'=>$this->getRankingStats(),
            'lastMatches'=>$this->getLastMatches(),
            'seasons'=>$this->getSeasons()
        ]);
    }
}