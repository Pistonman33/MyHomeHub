<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serie;
use App\Models\InfoSerie;
use App\Models\Support;
use App\Models\TMDB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Storage;


class SeriesController extends Controller
{
  private $tmdb;
  function __construct()
  {
    $this->tmdb = new TMDB();
  }

  public function pending(Request $request,$offset_url=0){
    $offset = 0;
    if($offset_url!==0){
      $request->session()->put('serie_pending_step', $offset_url);
      $offset = $offset_url;
    }else{
      if($request->session()->has('serie_pending_step')){
        $offset = $request->session()->get('serie_pending_step');
      }
    }

    if ($request->isMethod('post')) {
      // Search another serie
      if($request->exists('new_movie')) {
        return $this->prepareView($offset,$request->new_movie);
      }else{
        //SELECT THE MOVIE
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required',
            'infomovie_id' => 'required',
        ]);
        if(!$validator->errors()->any()){
          $movie =  Serie::find($request->movie_id);
          if($movie) {
            $result = $this->tmdb->getSerieDetails($request->infomovie_id);
            $image = env("THEMOVIEDB_IMG_URL").$result['poster_path'];
            $infoMovie = new InfoSerie();
            if(!$id = $infoMovie->storeInfoSerie($result,$image)){
              $validator->errors()->add('movie','Serie info not save');
            }
            $movie->fk_id_serie_info = $id;
            if(!$movie->save()){
              $validator->errors()->add('serie','Issue saving fk_id_serie_info');
            }

          }else{
            $validator->errors()->add('serie','Serie not found');
          }
        }
        if($validator->errors()->any())
          return $this->prepareView($offset)->withErrors($validator);
        else
          return $this->prepareView($offset)->with("success","Serie correctly updated");
      }
    } else {
      return $this->prepareView($offset);
    }
  }

  // FOR PENDING DISPLAY VIEW
  private function prepareView($offset,$title=false){
    $type_support= null;
    $info_movies = null;
    $all_pending_movies = Serie::whereNull('fk_id_serie_info')->orderBy('id','desc');
    $nb_movies = Serie::getCountSerieNotInfo();
    $current_movie = $all_pending_movies->offset($offset)->limit(1)->first();
    if($current_movie){
      $support = Support::find($current_movie->fk_id_support);
      $type_support = $support->type;
      $movie_title = ($title)? $title :$current_movie->title;
      $info_movies = $this->searchAllSerie($current_movie->id,$movie_title);  
    }

    if($offset == 0){
      $previous = null;
    } else {
      $previous = $offset-1;
    }
    if($offset >= $nb_movies-1){
      $next = null;
    } else {
      $next = $offset+1;
    }

    return view('backend.movies.pending')->with('current_movie', $current_movie)
                                 ->with('previous_movie', $previous)
                                 ->with('next_movie', $next)
                                 ->with('nb_movies', $nb_movies)
                                 ->with('support', $type_support)
                                 ->with('infomovies', $info_movies)
                                 ->with('type', 'tvshows')
                                 ->with('offset', $offset);
  }

  public function delete($movieid){
      Serie::find($movieid)->delete();
      return back();
  }

  private function searchAllSerie($id,$title){
    $series = $this->tmdb->searchSerieByName($title);
    $info_series = array();
    if($series !== null){
      foreach ($series as $serie) {
          $serieInfo = new \stdClass();
          $serieInfo->picture = env("THEMOVIEDB_IMG_URL").$serie['poster_path'];
          $serieInfo->title = $serie['original_name'];
          $serieInfo->description = $serie['overview'];
          $serieInfo->infomovie_id = $serie['id'];
          $serieInfo->movie_id = $id;
          array_push($info_series, $serieInfo);
      }
    }
    return $info_series;
  }


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
