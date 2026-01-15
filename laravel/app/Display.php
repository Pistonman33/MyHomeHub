<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Record;

class Display extends Model
{
    static function filesize($bytes){
      if ($bytes >= 1073741824)
      {
          $bytes = number_format($bytes / 1073741824, 2) . ' GB';
      }
      elseif ($bytes >= 1048576)
      {
          $bytes = number_format($bytes / 1048576, 2) . ' MB';
      }
      elseif ($bytes >= 1024)
      {
          $bytes = number_format($bytes / 1024, 2) . ' KB';
      }
      elseif ($bytes > 1)
      {
          $bytes = $bytes . ' bytes';
      }
      elseif ($bytes == 1)
      {
          $bytes = $bytes . ' byte';
      }
      else
      {
          $bytes = '0 bytes';
      }

      return $bytes;
    }

    static function file_last_modified($timestamp){
        return date("F jS, Y, H:i:s",$timestamp);
    }

    static function dateDMY($date){
        return date('d/m/Y', strtotime($date));
    }

    static function monthTextPrefixe($month){
        return date("M", mktime(0, 0, 0, $month, 1));
    }

    static function monthText($month){
        return date("F", mktime(0, 0, 0, $month, 1));
    }

    static function day($date){
        return date("d",strtotime($date));
    }

    static function year($date){
        return date("Y",strtotime($date));
    }

    static function transactionAmount(Record $transaction){
      $output= "&euro;&nbsp;";
      $output.= number_format($transaction->montant, 2);
      $output.= $transaction->retrait ? "&nbsp;-" : "&nbsp;+";
      return $output;
    }

    static function amount($amount){
      $output= "&euro;&nbsp;";
      $output.= number_format($amount, 2);
      return $output;
    }

    static function amountForChart($amount){
      return number_format($amount, 2, '.', '');
    }
}
