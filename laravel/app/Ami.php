<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ami extends Model
{
    static function getBirthdateByDate(){
      $query = Ami::selectRaw("lastname,firstname,nom,birthdate,
      			    MAKEDATE(YEAR(now()),DAYOFYEAR(CONCAT(YEAR(now()),'-',MONTH(birthdate),'-',DAY(birthdate)))) as annivdate ")
              ->join('groupe_amis','groupe_amis.id_groupe_ami','=','amis.fk_id_groupe_ami')
              ->whereRaw('DATE_FORMAT(birthdate,"%m-%d") = ?', array(date('m-d'))) ;
      return $query->get();
    }

    static function getBirthdateWeek($startdate,$enddate){
      $query = Ami::selectRaw("lastname,firstname,nom,birthdate,
      			    MAKEDATE(YEAR(now()),DAYOFYEAR(CONCAT(YEAR(now()),'-',MONTH(birthdate),'-',DAY(birthdate)))) as annivdate ")
              ->join('groupe_amis','groupe_amis.id_groupe_ami','=','amis.fk_id_groupe_ami')
              ->whereRaw('MAKEDATE(YEAR(now()),DAYOFYEAR(CONCAT(YEAR(now()),"-",MONTH(birthdate),"-",DAY(birthdate)))) BETWEEN ? AND ?', array($startdate,$enddate))
              ->orderBy('annivdate');
      return $query->get();
    }
}
