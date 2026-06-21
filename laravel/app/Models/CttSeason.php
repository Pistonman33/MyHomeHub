<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CttMatch;

class CttSeason extends Model
{
    protected $table = 'ctt_seasons';

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
        'ranking',
        'starting_points',
        'current_points',
        'ranking_belgium'
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
        'ranking'=> 'string',
        'starting_points',
        'current_points',
        'ranking_belgium'
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
}