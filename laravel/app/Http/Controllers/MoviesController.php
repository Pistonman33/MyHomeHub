<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Movie;
use App\Models\InfoMovie;
use App\Models\TMDB;
use App\Models\Support;
use Illuminate\Http\Response;
use finfo;
use Aws\CloudSearch\CloudSearchClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



use Storage;

class MoviesController extends Controller
{
  private $tmdb;
  function __construct()
  {
    $this->tmdb = new TMDB();
  }


  function home(Request $request)
  {
    $position = 0;
    $item_per_page = 10;
    $showcases = Movie::join('info_movie','info_movie.id','=','movies.fk_id_movie_info')
    ->orderBy('movies.date_access','desc')->limit(10)->get();    
    $all_support = Support::orderBy('type')->get();
    $filter_support = "All support";
    if($request->support){
      $sup = Support::find($request->support);
      $filter_support = $sup->type;
    }

    $all_genre = InfoMovie::orderBy('genre')->distinct('genre')->select('genre')->get()->toArray();
    $gen = end($all_genre);
    $filter_genre = "All genre";
    if($request->genre){
      $filter_genre = $request->genre;
    }

    $all_year = InfoMovie::orderBy('year')->distinct('year')->select('year')->get()->toArray();
    $filter_year ="All year";
    if($request->year){
      $filter_year = $request->year;
    }


    return view('movies.home')->with('all_support',$all_support)
                              ->with('filter_support',$filter_support)
                              ->with('all_genre',$all_genre)
                              ->with('filter_genre',$filter_genre)
                              ->with('all_year',$all_year)
                              ->with('showcases',$showcases)
                              ->with('filter_year',$filter_year);                              
  }

  public function filter(Request $request){
    $output="";

    if($request->ajax())
    {
        $item_per_page = 10;
        $page_number = $request->page;
        $position = (($page_number-1) * $item_per_page);
        $query=Movie::join('info_movie','info_movie.id','=','movies.fk_id_movie_info')
                         ->join('supports','movies.fk_id_support','=','supports.id');                         
        if($request->search){
          $query->where('movies.title','LIKE','%'.$request->search."%")
                ->orWhere('info_movie.title','LIKE','%'.$request->search."%")
                ->orWhere('info_movie.originalTitle','LIKE','%'.$request->search."%")
                ->orderBy('movies.title','asc');
        }else{
          $query->orderBy('movies.created_at','desc');
        }
        if($request->support && $request->support != "-1"){
          $query->where('movies.fk_id_support',$request->support);
        }
        if($request->genre && $request->genre != "-1"){
          $query->where('info_movie.genre','like', '%'.$request->genre.'%');
        }
        if($request->year && $request->year != "-1"){
          $query->where('info_movie.year',$request->year);
        }
        $total = $query->count();
        $movies = $query->limit($item_per_page)->offset($position)->get();
        $type = 'movies';
        $output = view("movies.list_mobile_2columns_cell",compact('movies','type'))->render();
    }
    return Response(array('total'=>$total,'nb'=>sizeof($movies),'content'=>$output));
  }

  /* OLD MOVIES ALL --- START*/
  public function all(Request $request){
    $all_support = Support::orderBy('type')->get();
    $sup = $all_support->first();
    $filter_support = $sup->type;
    $filter_supportid = $sup->id;
    if($request->support){
      $sup = Support::find($request->support);
      $filter_support = $sup->type;
      $filter_supportid = $sup->id;
    }

    $all_genre = InfoMovie::orderBy('genre')->distinct('genre')->select('genre')->get()->toArray();
    $gen = end($all_genre);
    $filter_genre = $gen['genre'];
    if($request->genre){
      $filter_genre = $gen->genre;
    }

    return view('movies.all')->with('all_support',$all_support)
                              ->with('filter_support',$filter_support)
                              ->with('filter_supportid',$filter_supportid)
                              ->with('all_genre',$all_genre)
                              ->with('filter_genre',$filter_genre);
  }

  public function search(Request $request){
    $output="";

    if($request->ajax())
    {
        $item_per_page = 32;
        $page_number = $request->page;
        $position = (($page_number-1) * $item_per_page);
        $movies=Movie::join('info_movie','info_movie.id','=','movies.fk_id_movie_info')
                         ->orderBy('movies.title','asc')->limit($item_per_page)->offset($position);
        if($request->search){
          $movies->where('movies.title','LIKE','%'.$request->search."%")
                ->orWhere('info_movie.title','LIKE','%'.$request->search."%")
                ->orWhere('info_movie.originalTitle','LIKE','%'.$request->search."%");
        }
        if($request->support){
          $movies->where('movies.fk_id_support',$request->support);
        }
        if($request->genre){
          $movies->where('movies.genre',$request->genre);
        }
        $result = $movies->get();
        $output = view("movies.movie_cell",compact('result'))->render();
    }
    return Response($output);
  }
  /* OLD MOVIES ALL --- END*/

  // BIG PROCESS
  public function checkValidPoster(){
    InfoMovie::checkValidPoster();
    return redirect('movies/check/picture');
  }
  public function checkPicture(Request $request,$offset_url=0){
    // SEARCH ALL MOVIES ISSUE WITH PICTURES
    $badMovieTitlePicture = InfoMovie::where('valid_poster',false)->get();
    // PREPARE VIEW TO DISPLAY PICTURES TO SEARCH NEW PICTURES
    $offset = 0;
    if($offset_url!==0){
      $request->session()->put('pictures_step', $offset_url);
      $offset = $offset_url;
    }else{
      if($request->session()->has('pictures_step')){
        $offset = $request->session()->get('pictures_step');
      }
    }
    if($offset >= sizeof($badMovieTitlePicture)){
      $request->session()->put('pictures_step', 0);
      $offset = 0;
    }

    if ($request->isMethod('post')) {
      // Search another movie
      if($request->exists('new_movie')) {
        if($request->exists('isSerie')){
          $type = "tvseries";
        }else{
          $type = "movie";
        }
        return $this->preparePictureView($badMovieTitlePicture,$offset,$request->new_movie,$type);
      }else{
        if($request->exists('new_img') && $request->exists('info_movie_id')){
          $info_movie = InfoMovie::find($request->info_movie_id);
          $items=explode(".",$request->new_img);
          $img_data = file_get_contents(env("THEMOVIEDB_IMG_URL").$request->new_img);
          $extension = end($items);
          if(strlen(trim($info_movie->poster)) >0 && Storage::exists(env("STORAGE_MOVIES_IMG_PATH").$info_movie->poster)){
            Storage::delete(env("STORAGE_MOVIES_IMG_PATH").$info_movie->poster);
          }
          $filename = $info_movie->id.".".$extension;
          $info_movie->poster = $filename;
          $info_movie->valid_poster = true;
          $info_movie->save();
          Storage::put(env("STORAGE_MOVIES_IMG_PATH").$filename, $img_data);
          return redirect('movies/check/picture');
        }
      }
    } else {
      return $this->preparePictureView($badMovieTitlePicture,$offset);
    }  
      
  }

  
  public function savePicture(){
    $movies = InfoMovie::all();
    foreach($movies as $info_movie){
      if($info_movie->picture){
        $mime_type = (new finfo(FILEINFO_MIME))->buffer($info_movie->picture);
        $extension = str_replace("image/","",explode(";",$mime_type))[0];
        $filename = $info_movie->id.".".$extension;
        $info_movie->poster = $filename;
        $info_movie->save();
        Storage::put(env("STORAGE_MOVIES_IMG_PATH").$filename, $info_movie->picture);    
      }
    }   
  }

  public function picture($id){
    $info_movie = InfoMovie::find($id);
    // Return the image in the response with the correct MIME type
    return response()->make($info_movie->picture, 200, array(
        'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($info_movie->picture)
    ));
  }

  public function pending(Request $request,$offset_url=0){
    $offset = 0;
    if($offset_url!==0){
      $request->session()->put('pending_step', $offset_url);
      $offset = $offset_url;
    }else{
      if($request->session()->has('pending_step')){
        $offset = $request->session()->get('pending_step');
      }
    }

    if ($request->isMethod('post')) {
      // Search another movie
      if($request->exists('new_movie')) {
        return $this->prepareView($offset,$request->new_movie);
      }else{
        //SELECT THE MOVIE
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required',
            'infomovie_id' => 'required',
        ]);
        if(!$validator->errors()->any()){
          $movie =  Movie::find($request->movie_id);
          if($movie) {
            $result = $this->tmdb->getMovieDetails($request->infomovie_id);
            $image = env("THEMOVIEDB_IMG_URL").$result['poster_path'];
            $infoMovie = new InfoMovie();
            if(!$id = $infoMovie->storeInfoMovie($result,$image)){
              $validator->errors()->add('movie','Movie info not save');
            }
            $movie->fk_id_movie_info = $id;
            if(!$movie->save()){
              $validator->errors()->add('movie','Issue saving fk_id_movie_info');
            }

          }else{
            $validator->errors()->add('movie','Movie not found');
          }
        }
        if($validator->errors()->any())
          return $this->prepareView($offset)->withErrors($validator);
        else
          return $this->prepareView($offset)->with("success","Movie correctly updated");
      }
    } else {
      return $this->prepareView($offset);
    }
  }

  // FOR PENDING DISPLAY VIEW
  private function prepareView($offset,$title=false){
    $type_support= null;
    $info_movies = null;
    $all_pending_movies = Movie::whereNull('fk_id_movie_info')->orderBy('id','desc');
    $nb_movies = Movie::getCountMovieNotInfo();
    $current_movie = $all_pending_movies->offset($offset)->limit(1)->first();
    if($current_movie){
      $support = Support::find($current_movie->fk_id_support);
      $type_support = $support->type;
      $movie_title = ($title)? $title :$current_movie->title;
      $info_movies = $this->searchAllMovie($current_movie->id,$movie_title);  
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

    return view('movies.pending')->with('current_movie', $current_movie)
                                 ->with('previous_movie', $previous)
                                 ->with('next_movie', $next)
                                 ->with('nb_movies', $nb_movies)
                                 ->with('support', $type_support)
                                 ->with('infomovies', $info_movies)
                                 ->with('type', 'movies')
                                 ->with('offset', $offset);
  }

  public function delete($movieid){
      Movie::find($movieid)->delete();
      return back();
  }

  
  private function searchAllMovie($id,$title,$type='movie'){
    $movies = $this->tmdb->searchByName($title);
    $info_movies = array();
    if($movies !== null){
      foreach ($movies as $movie) {
          $movieInfo = new \stdClass();
          $movieInfo->picture = env("THEMOVIEDB_IMG_URL").$movie['poster_path'];
          $movieInfo->title = $movie['original_title'];
          $movieInfo->description = $movie['overview'];
          $movieInfo->infomovie_id = $movie['id'];
          $movieInfo->movie_id = $id;
          array_push($info_movies, $movieInfo);
      }
    }
    return $info_movies;
  }

  public function checkDBSQlite(Request $request){
    $movies = DB::connection('sqlite')->select('select id_file,moviename from mymov_file');
    foreach($movies as $movie){
      if($mov_exist = Movie::where('title', $movie->moviename)->Where('fk_id_support', 3)->first()){
/*        $id = $mov_exist->id;
        $id_file = $movie->id_file;
        $affected = DB::connection('sqlite')->update('update mymov_file set fk_id_movie = ? where id_file = ?', [$id,$id_file]);
*/
      }else{
        echo "Movie: ".$movie->moviename.' new?<hr/>';
      }
    }
  }

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
