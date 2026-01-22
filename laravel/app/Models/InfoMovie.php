<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class InfoMovie extends Model
{
    protected $table = 'info_movie';

    public function storeInfoMovie($result,$image){
      $actors = collect($result["credits"]["cast"])->take(5)->implode('name', ',');
      $directors = collect($result["credits"]["crew"])->where('department', 'Directing')->take(1)->implode('name', ',');
      $this->originalTitle = $result['original_title'];
      $this->title = $result['title'];
      $this->genre = $result['genres'][0]["name"];
      $this->year = explode("-",$result["release_date"])[0];
      $this->directors= $directors;
      $this->duration = $this->minutesToTime($result["runtime"]);
      $this->actors = $actors;
      $this->synopsis = $result["overview"];
      $this->allo_code = $result['id'];
      $this->save();
      // IMAGE DOWNLOAD
      $this->poster = $this->create_img($image,$this->id);
      $this->save();
      return $this->id;
    }

    private function secondsToTime($seconds) {
  	    $dtF = new \DateTime("@0");
  	    $dtT = new \DateTime("@$seconds");
  	    return $dtF->diff($dtT)->format('%h h, %i min');
  	}

    private function minutesToTime($minutes) {
  	    return intdiv($minutes, 60).' h, '. ($minutes % 60).' min';
    }

  	private function create_img($imgfile,$id){
  	    if(!empty($imgfile)){
  	        $extension = explode(".",$imgfile);
  	        $extension = $extension[sizeof($extension)-1];
            $filename = $id.".".$extension;
  	        $data = file_get_contents($imgfile);
            Storage::put(env("STORAGE_MOVIES_IMG_PATH").$filename, $data);    
  	        return $filename;
  	    }
  	    return null;
  	}

    static function getInfoMovieByAlloCode($code){
      return InfoMovie::where('allo_code', $code)->first();
    }

    static function checkValidPoster(){
      $movies = InfoMovie::where('valid_poster',false)->limit(100)->get();
      foreach($movies as $info_movie){
        if($info_movie->poster){
          $filename = url("storage/images/movies/$info_movie->poster");
          $resolution = getimagesize($filename);
          $width = $resolution[0];
          $height = $resolution[1];
          if($width >= 500){
            $info_movie->valid_poster = true;
            $info_movie->save();
          }
        }
      }
    }

}
