<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    static function getCountAllSerie(){
        return Serie::whereNotNull('fk_id_serie_info')->count();
    }
  
    static function getCountSerieNotInfo(){
        return Serie::whereNull('fk_id_serie_info')->count();
    }
  
}
