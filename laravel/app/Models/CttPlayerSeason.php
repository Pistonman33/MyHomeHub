<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CttPlayerSeason extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'ctt_player_seasons';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'year',
        'player_license',
        'ranking',
        'starting_points',
        'current_points',
        'ranking_belgium',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'year' => 'integer',
        'starting_points' => 'decimal:2',
        'current_points' => 'decimal:2',
        'ranking_belgium' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function season()
    {
        return $this->belongsTo(CttSeason::class, 'year', 'year');
    }

    public function player()
    {
        return $this->belongsTo(CttPlayer::class, 'player_license', 'license');
    }

}