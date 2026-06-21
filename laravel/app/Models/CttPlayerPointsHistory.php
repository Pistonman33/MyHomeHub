<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CttPlayerPointsHistory extends Model
{
    protected $table = 'ctt_player_points_history';

    protected $primaryKey = 'match_id';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'match_id',
        'delta_points',
        'opponent_points',
    ];

    public function match()
    {
        return $this->belongsTo(CttMatch::class, 'match_id');
    }
}