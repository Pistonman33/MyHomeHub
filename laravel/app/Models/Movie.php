<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    static function getCountAllMovie(){
      return Movie::whereNotNull('fk_id_movie_info')->count();
    }

    static function getCountMovieNotInfo(){
      return Movie::whereNull('fk_id_movie_info')->count();
    }
}
