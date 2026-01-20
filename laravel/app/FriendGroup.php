<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FriendGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function friends()
    {
        return $this->hasMany(Friend::class, 'fk_id_friend_group');
    }
}
