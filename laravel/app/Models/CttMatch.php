<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CttMatch extends Model
{
    protected $table = 'ctt_matches';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'match_unique_id',
        'match_id',
        'player_license',
        'season_year',
        'date',
        'competition_type',
        'opponent_license',
        'opponent_firstname',
        'opponent_lastname',
        'opponent_ranking',
        'opponent_club',
        'result',
        'set_for',
        'set_against',
        'ranking_evaluation_category',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'match_unique_id' => 'integer',
        'player_license' => 'integer',
        'season_year' => 'integer',
        'opponent_license' => 'integer',
        'date' => 'date',
        'set_for' => 'integer',
        'set_against' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function player()
    {
        return $this->belongsTo(CttPlayer::class, 'player_license', 'license');
    }

    public function season()
    {
        return $this->belongsTo(CttSeason::class, 'season_year', 'year');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isWin()
    {
        return $this->result === 'V';
    }

    public function isLoss()
    {
        return $this->result === 'D';
    }
}