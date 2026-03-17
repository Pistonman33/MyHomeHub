<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CttPlayer extends Model
{
    protected $table = 'ctt_players';

    // Primary key is 'license' and it's not auto-incrementing
    protected $primaryKey = 'license';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'license',
        'firstname',
        'lastname',
        'status',
        'club',
    ];

    protected $casts = [
        'license' => 'integer',
    ];
}