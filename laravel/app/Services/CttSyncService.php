<?php

namespace App\Services;

use SoapClient;
use App\Models\CttPlayer;
use App\Models\CttSeason;
use App\Models\CttMatch;

class CttSyncService
{
    protected $wsdl = "https://api.vttl.be/?wsdl";

    public function sync(int $license = null, int $year = null)
    {
        $client = new SoapClient($this->wsdl, [
            'trace' => true,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        ]);
        $seasonFilter = substr($year, 2);

        $params = [
            "UniqueIndex" => $license,
            "Season" => $seasonFilter,
            "WithResults" => true,
            "WithOpponentRankingEvaluation" => true
        ];
        // Get all info about player with license and season filter
        $response = $client->__soapCall("GetMembers", [$params]);
        
        // No info for this player and season, we stop here
        if(!isset($response->MemberEntries) or empty($response->MemberEntries)) {
            return "No player for licence $license and season $year found in CTT API.";
        }

        $member = $response->MemberEntries;
        
        // create or update season in db
        $season = CttSeason::updateOrCreate(
            ['year' => $year],
            [
                'name' => ($year-1)."-$year",
                'is_current' => $year == date('Y')  ? true : false,
                'ranking' => $member->Ranking ?? null,
            ]
        );
        // create or update player in db
        $player = CttPlayer::updateOrCreate(
            ['license' => $member->UniqueIndex],
            [
                'firstname' => $member->FirstName,
                'lastname' => $member->LastName,                
                'status' => $member->Status ?? null,
                'club' => $member->Club ?? null,
            ]
        );
        
        // Store matches in db
        foreach ($member->ResultEntries ?? [] as $matchData) {

            CttMatch::updateOrCreate(
                [
                'player_license' => $player->license,
                'date' => $matchData->Date,
                'opponent_license' => $matchData->UniqueIndex ?? null,
                ],
                [
                    'match_id' => $matchData->MatchId ?? null, 
                    'match_unique_id' => $matchData->MatchUniqueId ?? null, 
                    'player_license' => $player->license,                  
                    'season_year' => $year,
                    'competition_type' => $matchData->CompetitionType ?? null,
                    
                    'opponent_firstname' => $matchData->FirstName ?? null,
                    'opponent_lastname' => $matchData->LastName ?? null,
                    'opponent_ranking' => $matchData->Ranking ?? null,
                    'opponent_club' => $matchData->Club ?? null,

                    'tournament_name' => $matchData->TournamentName ?? null,
                    'tournament_serie' => $matchData->TournamentSerieName ?? null,                    

                    'result' => $matchData->Result ?? 'D',
                    'set_for' => $matchData->SetFor ?? 0,
                    'set_against' => $matchData->SetAgainst ?? 0,
                    'ranking_evaluation_category' => $matchData->RankingEvaluationCategory ?? null,
                    'ranking_diff' => isset($matchData->Ranking) && isset($member->Ranking) ?
                        $this->calculateRankingDiff($member->Ranking, $matchData->Ranking) : null,
                ]
            );
        }
        return "Sync completed for license $license and season $year.";
    }

    protected function calculateRankingDiff($playerRanking, $opponentRanking)
    {
        $map = [
            'NG' => 0,
            'E6' => 1,
            'E4' => 2,
            'E2' => 3,
            'E0' => 4,
            'D6' => 5,
            'D4' => 6,
            'D2' => 7,
            'D0' => 8,
            'C6' => 9,
            'C4' => 10,
            'C2' => 11,
            'C0' => 12,
            'B6' => 13,
            'B4' => 14,
            'B2' => 15,
            'B0' => 16,
        ];

        return ($map[$opponentRanking] ?? 0) - ($map[$playerRanking] ?? 0);
    }
}