<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Movie;
use App\Models\Support;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class MoviesController extends Controller
{

  // API FUNCTION
  public function saveMovie(Request $request){
    // TODO
    //      6.Run script on syno with new file_db.db modified
    //      7.Compare nb movies from syno and ovh where fk_id_support =3

    //$data = Auth::guard('api')->user()->name;
    $validator = Validator::make($request->all(), [
            'title' => 'required',
            'supportid' => 'required|numeric'
    ]);
    if ($validator->fails()) {
      return response()->json(['error' => $validator->messages()->first()], 403);
    }
    $mov_exist = Movie::where('title', $request['title'])->first();
    if(!$mov_exist){
      $newmovie = new Movie();
      $newmovie->title = $request['title'];
      $newmovie->fk_id_support = $request['supportid'];
      $newmovie->date_access = $request['date_access'];
      if(isset($request['location'])) {
          $newmovie->location = $request['location'];
      }
      Log::info("[MovieController] - API new movie: \r\n" . print_r($newmovie,true));
      if($id = $newmovie->save()){
          $data = array(
           'id_movie' => $newmovie->id
          );
          Log::info("[MovieController] - API response movies call: \r\n" . print_r($data,true));
          return response()->json($data);
      } else {
          return response()->json(['error' => "Issue to create new movie: ".$request['title']], 403);
      }
    }else{
      Log::info("[MovieController] - date_access: \r\n" . print_r($request['date_access'],true));
      $mov_exist->date_access = $request['date_access'];
      if(isset($request['location'])) {
          $mov_exist->location = $request['location'];
      }
      $mov_exist->save();
      Log::info("[MovieController] - API update movie: \r\n" . print_r($mov_exist,true));
      $data = array(
       'id_movie' => $mov_exist->id
      );
      Log::info("[MovieController] - API response movies call: \r\n" . print_r($data,true));
      return response()->json($data);
    }
  }
}