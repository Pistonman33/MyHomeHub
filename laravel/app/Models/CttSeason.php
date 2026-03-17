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
        'ranking'
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