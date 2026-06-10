<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Friend;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayMail;

class Birthday extends Model
{
    static function sentBirthdayMail(){
      $amis = Friend::getBirthdateByDate();
      if($amis && count($amis) > 0){
        $contentMail = "<p>Aujourd'hui, c'est l'anniversaire de :</p>";
      	foreach($amis as $ami){
          $birthdate = Carbon::parse($ami->birthdate);
      		$age = $birthdate->diffInYears($ami->annivdate);
      		$contentMail.="<strong>$ami->lastname $ami->firstname</strong> à ".$age." ans ($ami->name)<br/>";
      	}
        try {
          Mail::to(env('MAIL_TO'))->send(new BirthdayMail($contentMail));
          Log::info('Birthday email sent', ['type' => 'daily', 'recipient' => env('MAIL_TO')]);
        } catch (\Throwable $e) {
          Log::error('Birthday email failed', [
            'type' => 'daily',
            'recipient' => env('MAIL_TO'),
            'message' => $e->getMessage(),
            'exception' => get_class($e),
          ]);
        }
      }

    }

    static function sentMonthlyBirthdayMail(){
      $startdate = date('Y-m-d');
      $enddate = date("Y-m-d", strtotime("+1 week"));
      $amis = Friend::getBirthdateWeek($startdate,$enddate);
      if($amis && count($amis) > 0){
        $contentMail = "Voici le calendrier d'anniversaire de cette semaine:<br/><br/>";
        foreach($amis as $ami){
          $birthdate = Carbon::parse($ami->birthdate);
      		$age = $birthdate->diffInYears($ami->annivdate);
          $contentMail.="[".date("l d F Y",strtotime($ami->annivdate))."] <strong>$ami->lastname $ami->firstname </strong>à ".$age." ans ($ami->name)<br/>";
        }
        try {
          Mail::to(env('MAIL_TO'))->send(new BirthdayMail($contentMail));
          Log::info('Birthday email sent', ['type' => 'monthly', 'recipient' => env('MAIL_TO')]);
        } catch (\Throwable $e) {
          Log::error('Birthday email failed', [
            'type' => 'monthly',
            'recipient' => env('MAIL_TO'),
            'message' => $e->getMessage(),
            'exception' => get_class($e),
          ]);
        }
      }
    }
}