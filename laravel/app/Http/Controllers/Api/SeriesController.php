<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serie;
use App\Models\Support;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class SeriesController extends Controller
{
  // API FUNCTION
  public function saveSerie(Request $request){
    // TODO
    //      6.Run script on syno with new file_db.db modified
    //      7.Compare nb series from syno and ovh where fk_id_support =3

    //$data = Auth::guard('api')->user()->name;
    $validator = Validator::make($request->all(), [
            'title' => 'required',
            'supportid' => 'required|numeric'
    ]);
    if ($validator->fails()) {
      return response()->json(['error' => $validator->messages()->first()], 403);
    }
    $ser_exist = Serie::where('title', $request['title'])->first();
    if(!$ser_exist){
      $newserie = new Serie();
      $newserie->title = $request['title'];
      $newserie->fk_id_support = $request['supportid'];
      $newserie->date_access = $request['date_access'];
      if(isset($request['location'])) {
          $newserie->location = $request['location'];
      }
      Log::info("[SeriesController] - API new serie: \r\n" . print_r($newserie,true));
      if($id = $newserie->save()){
          $data = array(
           'id_serie' => $newserie->id
          );
          Log::info("[SeriesController] - API response series call: \r\n" . print_r($data,true));
          return response()->json($data);
      } else {
          return response()->json(['error' => "Issue to create new serie: ".$request['title']], 403);
      }
    }else{
      Log::info("[SeriesController] - date_access: \r\n" . print_r($request['date_access'],true));
      $ser_exist->date_access = $request['date_access'];
      if(isset($request['location'])) {
        $ser_exist->location = $request['location'];
      }
      $ser_exist->save();
      Log::info("[SeriesController] - API update serie: \r\n" . print_r($ser_exist,true));
      $data = array(
       'id_serie' => $ser_exist->id
      );
      Log::info("[SeriesController] - API response series call: \r\n" . print_r($data,true));
      return response()->json($data);
    }
  }
}