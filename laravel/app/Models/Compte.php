<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
  public function IsCompteExists(){
    if(strlen(trim($this->num)) !== 0){
      $compte = Compte::where("num",$this->num)->first();
      if(isset($compte) && $compte->compteid){
        $this->compteid=$compte->compteid;
        return true;
      }else{
        return false;
      }
    }return false;
  }

}
