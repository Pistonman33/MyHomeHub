<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CttMatch;
use App\Models\CttPlayerSeason;

class CttSeason extends Model
{
    protected $table = 'ctt_seasons';

    public static function currentSeasonYear(): int
    {
        $now = now();

        return $now->month >= 9 ? $now->year + 1 : $now->year;
    }

    /*
    |--------------------------------------------------------------------------
    | Primary Key Configuration
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'year';
    public $incrementing = false;
    protected $keyType = 'int';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'year',
        'name',
        'is_current',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'year' => 'integer',
        'name' => 'string',
        'is_current' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function matches()
    {
        return $this->hasMany(CttMatch::class, 'season_year', 'year');
    }

    public function playerSeasons()
    {
        return $this->hasMany(CttPlayerSeason::class, 'year', 'year');
    }

 
}