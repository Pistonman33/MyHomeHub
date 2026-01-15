<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\UpdateRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Record extends Model
{
    // STATS SECTION
    static function getSumPriceDepotRetrait($date,$retrait) {
      $query = Record::selectRaw(DB::raw('SUM(montant) as total'))
              ->where('date','like',$date.'%')
              ->where('deleted',0)
              ->where('validate',1)
              ->where('retrait',$retrait);
      return $query->first();
    }

    static function getSumPriceDepotRetraitByCategorie($date,$retrait) {
      $query = Record::selectRaw(DB::raw('SUM(montant) as total, nom, fk_id_categorie'))
              ->join('categories','categories.id','=','records.fk_id_categorie')
              ->where('date','like',$date.'%')
              ->where('deleted',0)
              ->where('validate',1)
              ->where('retrait',$retrait)
              ->groupBy('fk_id_categorie')
              ->groupBy('nom')
              ->orderBy('total','desc');
      return $query->get();
    }

    static function getSumPriceDepotRetraitByMonth($start_date,$end_date,$retrait,$categorie) {
      $query = Record::selectRaw(DB::raw('SUM(montant) as total, DATE_FORMAT(date, "%y-%m") as dd'))
              ->where('deleted',0)
              ->where('validate',1)
              ->where('retrait',$retrait)
              ->where('date','>=',$start_date)
              ->where('date','<',$end_date)
              ->whereIn('fk_id_categorie',$categorie)
              ->groupBy('dd')
              ->orderBy('dd');
      //var_dump($date);
      //var_dump($retrait);
      //var_dump($categorie);
      //dd($query->toSql());
      return $query->get();

    }

    static function getSumPriceDepotRetraitByYear($retrait,$categorie) {
      $query = Record::selectRaw(DB::raw('SUM(montant) as total, DATE_FORMAT(date, "%Y") as dd'))
              ->where('deleted',0)
              ->where('validate',1)
              ->where('retrait',$retrait)
              ->whereIn('fk_id_categorie',$categorie)
              ->groupBy('dd')
              ->orderBy('dd');
      return $query->get();
    }

    static function getLastTransactionDateEncoded(){
        $record = Record::where('deleted',0)->where('validate',1)->orderBy('date','desc')->limit(1)->first();
        return $record->date;
    }


    // IMPORT SECTION
    static function importFile($disk,$filename){
      ini_set('auto_detect_line_endings', 1);
      $content = $disk->get($filename);
      $lines = explode("\n",$content);
      $cpt_records = 0;
      foreach($lines as $linenum => $line){
        $line = mb_convert_encoding($line, 'UTF-8', 'ISO-8859-1');
        $line = str_ireplace("\"","",$line);
        $cols = explode(";",$line);
        if($linenum !==0 && is_array($cols) && sizeof($cols) > 9 && strlen(trim($cols[5]))!== 0){
          $currentdate = date("Y-m-d", strtotime(str_replace('/', '-', str_ireplace("\"","",$cols[5]))));
          $compte=new Compte();
          $compte->num = substr(trim($cols[0]),0,16);
          if($compte->IsCompteExists()){
            if(!Record::IsMouvementExists(trim($cols[3]),$compte->compteid,$currentdate)){
              $col = new Record();
              $col->fk_id_compte = $compte->compteid;
              $col->mouvement = trim($cols[3]);
              $col->date = $currentdate;
              $cols[6] = str_replace(" ","",$cols[6]);
              if(substr($cols[6],0,1)=='-'){
                $col->montant = trim(substr($cols[6],1,strlen($cols[6])));
                $col->retrait=1;
              }else{
                $col->montant = trim($cols[6]);
                $col->retrait=0;
              }
              //CORRECTION 4 SEPTEMBRE 2012 Montant ING avec des . pour les milliers et des virgules pour cents.
              $col->montant = str_replace(".","", $col->montant);
              $col->montant = str_replace(",",".", $col->montant);
              $col->libelle = "";
              $col->details = trim(utf8_decode($cols[8])).' '.trim(utf8_decode($cols[9])).' '.trim(utf8_decode($cols[10]) );
              $col->validate=0;
              $col->deleted=0;
              $col->save();
              $cpt_records++;
            }
          }else{
            echo 'nouveau compte:'.$compte->num.'!!!';
            die();
          }
        }
      }
      return array($cpt_records." transactions added");
    }

    static function assignCategoryRecords(){
      return UpdateRecords::doQueries();
    }

    static function IsMouvementExists($mouvement,$compteid,$currentdate){
      return Record::where('mouvement', $mouvement)->where('fk_id_compte',$compteid )->where('date', $currentdate)->exists();
  	}

    static function backup(){
      Artisan::call('backup:run');
      $output = Artisan::output();
      // log the results
      Log::info("[FinanceController] - Backup before add transactions file uploaded: \r\n" . $output);
    }

    static function restore($restore_file){
      $command = "unzip -p $restore_file";
      exec($command,$lines);

      // Temporary variable, used to store current query
      $templine = '';

      $error = '';

      // Loop through each line
      foreach ($lines as $line){
          // Skip it if it's a comment
          if(substr($line, 0, 2) == '--' || $line == ''){
              continue;
          }

          // Add this line to the current segment
          $templine .= $line;

          // If it has a semicolon at the end, it's the end of the query
          if (substr(trim($line), -1, 1) == ';'){
              // Perform the query
              if(!DB::unprepared($templine)){
                  $error .= 'Error performing query "<b>' . $templine . '</b>": ' . $db->error . '<br /><br />';
              }

              // Reset temp variable to empty
              $templine = '';
          }
      }
      return !empty($error)?$error:true;
    }
}
