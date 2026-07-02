<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CttPlayerPointsHistory extends Model
{
    protected $table = 'ctt_player_points_history';

    protected $fillable = [
        'match_id',
        'player_license',
        'delta_points',
        'opponent_points',
    ];

    protected $casts = [
        'delta_points' => 'decimal:2',
        'opponent_points' => 'decimal:2',
    ];

    public function match()
    {
        return $this->belongsTo(CttMatch::class, 'match_id');
    }
}