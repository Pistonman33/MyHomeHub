<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['lastname', 'firstname', 'birthdate', 'fk_id_friend_group'];

     protected $casts = [
        'birthdate' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(FriendGroup::class, 'fk_id_friend_group');
    }
}
