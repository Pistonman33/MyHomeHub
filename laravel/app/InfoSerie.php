<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;
use Carbon\Carbon;

class InfoSerie extends Model
{
    protected $table = 'info_series';

    public function storeInfoSerie($result,$image){
      $actors = collect($result["credits"]["cast"])->take(5)->implode('name', ',');
      $directors = collect($result["credits"]["crew"])->where('department', 'Directing')->take(1)->implode('name', ',');
      $creators = collect($result["created_by"])->take(5)->implode('name', ',');
      $this->originalTitle = $result['original_name'];
      $this->title = $result['name'];
      $this->genre = $result['genres'][0]["name"];
      $this->year = Carbon::parse($result["first_air_date"])->year;
      $this->actors = $actors;
      $this->directors= $directors;
      $this->synopsis = $result["overview"];
      $this->allo_code = $result['id'];
      $this->creators = $creators;
      $this->yearStart = $this->year;
      $this->yearEnd = Carbon::parse($result["last_air_date"])->year;
      $this->seasonCount = $result["number_of_seasons"];
      $this->episodeCount = $result["number_of_episodes"];
      if($result["in_production"] == false){
        $this->lastSeasonNumber= $this->seasonCount;        
      }else{
        $this->lastSeasonNumber= 0;        
      }
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

  	private function create_img($imgfile,$id){
  	    if(!empty($imgfile)){
  	        $extension = explode(".",$imgfile);
  	        $extension = $extension[sizeof($extension)-1];
            $filename = $id.".".$extension;
  	        $data = file_get_contents($imgfile);
            Storage::put(env("STORAGE_SERIES_IMG_PATH").$filename, $data);    
  	        return $filename;
  	    }
  	    return null;
  	}

    static function getInfoSerieByAlloCode($code){
      return InfoSerie::where('allo_code', $code)->first();
    }

    static function checkValidPoster(){
      $series = InfoSerie::where('valid_poster',false)->limit(100)->get();
      foreach($series as $info_serie){
        if($info_serie->poster){
          $filename = url("storage/images/series/$info_serie->poster");
          $resolution = getimagesize($filename);
          $width = $resolution[0];
          $height = $resolution[1];
          if($width >= 500){
            $info_serie->valid_poster = true;
            $info_serie->save();
          }
        }
      }
    }
}
