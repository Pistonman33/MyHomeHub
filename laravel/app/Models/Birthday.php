<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ami;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayMail;

class Birthday extends Model
{
    static function sentBirthdayMail(){
      $amis = Ami::getBirthdateByDate();
      if($amis && count($amis) > 0){
        $contentMail = "<p>Aujourd'hui, c'est l'anniversaire de :</p>";
      	foreach($amis as $ami){
          $birthdate = Carbon::parse($ami->birthdate);
      		$age = $birthdate->diffInYears($ami->annivdate);
      		$contentMail.="<strong>$ami->lastname $ami->firstname</strong> à ".$age." ans ($ami->nom)<br/>";
      	}
        Mail::to(env('MAIL_TO'))->send(new BirthdayMail($contentMail));
      }

    }

    static function sentMonthlyBirthdayMail(){
      $startdate = date('Y-m-d');
      $enddate = date("Y-m-d", strtotime("+1 week"));
      $amis = Ami::getBirthdateWeek($startdate,$enddate);
      if($amis && count($amis) > 0){
        $contentMail = "Voici le calendrier d'anniversaire de cette semaine:<br/><br/>";
        foreach($amis as $ami){
          $birthdate = Carbon::parse($ami->birthdate);
      		$age = $birthdate->diffInYears($ami->annivdate);
          $contentMail.="[".date("l d F Y",strtotime($ami->annivdate))."] <strong>$ami->lastname $ami->firstname </strong>à ".$age." ans ($ami->nom)<br/>";
        }
        Mail::to(env('MAIL_TO'))->send(new BirthdayMail($contentMail));
      }
    }
}
