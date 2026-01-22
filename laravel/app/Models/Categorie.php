<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    private static $colors = array("#ECDB54","#E94B3C","#6F9FD8","#944743","#DBB1CD","#EC9787","#00A591","#6B5B95","#6C4F3D"
                    ,"#BC70A4","#BFD641","#2E4A62","#B4B7BA","#C0AB8E","#92B558","#DC4C46","#672E3B","#C48F65"
                  ,"#223A5E","#898E8C","#005960","#9C9A40","#4F84C4","#D2691E","#578CA9","#F6D155","#004B8D","#F2552C","#95DEE3","#EDCDC2"
                ,"#CE3175","#5A7247","#CFB095","#4C6A92","#92B6D5","#838487","#B93A32","#AF9483","#AD5D5D","#006E51","#D8AE47","#9E4624","#B76BA3");

    public function getColor(){
        return self::$colors[$this->id - 16];
    }

    static function getColorById($id){
        return self::$colors[$id - 16];
    }

    static function getColorRGBAWithOpacity($id,$alpha){
      $color = self::$colors[$id - 16];
      $split = str_split(substr($color,1), 2);
      $r = hexdec($split[0]);
      $g = hexdec($split[1]);
      $b = hexdec($split[2]);
      return "rgba(" . $r . ", " . $g . ", " . $b . ", " . $alpha . ")";
    }
}
