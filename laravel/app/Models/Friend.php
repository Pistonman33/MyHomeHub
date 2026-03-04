<?php

namespace App\Models;

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

    static function getBirthdateByDate(){
      $query = Friend::selectRaw("lastname,firstname,name,birthdate,
      			    MAKEDATE(YEAR(now()),DAYOFYEAR(CONCAT(YEAR(now()),'-',MONTH(birthdate),'-',DAY(birthdate)))) as annivdate ")
              ->join('friend_groups','friend_groups.id','=','friends.fk_id_friend_group')
              ->whereRaw('DATE_FORMAT(birthdate,"%m-%d") = ?', array(date('m-d'))) ;
      return $query->get();
    }

    static function getBirthdateWeek($startdate,$enddate){
      $query = Friend::selectRaw("lastname,firstname,name,birthdate,
      			    MAKEDATE(YEAR(now()),DAYOFYEAR(CONCAT(YEAR(now()),'-',MONTH(birthdate),'-',DAY(birthdate)))) as annivdate ")
              ->join('friend_groups','friend_groups.id','=','friends.fk_id_friend_group')
              ->whereRaw('MAKEDATE(YEAR(now()),DAYOFYEAR(CONCAT(YEAR(now()),"-",MONTH(birthdate),"-",DAY(birthdate)))) BETWEEN ? AND ?', array($startdate,$enddate))
              ->orderBy('annivdate');
      return $query->get();
    }


}