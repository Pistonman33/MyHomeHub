<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Charge;

class ChargeController extends Controller
{
    public function home(){
      return view('charge.home');
    }

    public function save(Request $request){
      $previous_charge = Charge::orderBy('date_conso', 'desc')->first();
      if($request->isMethod('post')){
        $this->validate($request, [
            'date' => 'required|date_format:m/d/Y',
            'gaz' => 'required|numeric',
            'elec' => 'required|numeric',
            'water' => 'required|numeric',
        ]);
        $charge = new Charge();
        $myDateTime = \DateTime::createFromFormat('d/m/Y', $request->date);
        $charge->date_conso = $myDateTime->format('Y-m-d');
        $charge->elec_conso = $request->elec;
        $charge->gaz_conso = $request->gaz;
        $charge->eau_conso = $request->water;
        if(!$previous_charge){
          $charge->elec_conso_month = 0;
          $charge->gaz_conso_month = 0;
          $charge->eau_conso_month = 0;
        }else{
          $charge->elec_conso_month = number_format((str_ireplace(",",".",$charge->elec_conso) - $previous_charge->elec_conso), 1, '.', '');
          $charge->gaz_conso_month = number_format((str_ireplace(",",".",$charge->gaz_conso) - $previous_charge->gaz_conso), 3, '.', '');
          $charge->eau_conso_month = number_format((str_ireplace(",",".",$charge->eau_conso) - $previous_charge->eau_conso), 4, '.', '');
        }
        $charge->save();
        $previous_charge = $charge;
      }
      return view('charge.save')->with('previous_charge',$previous_charge);
    }
}
