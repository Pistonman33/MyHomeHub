<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\InfoMovie;
use App\Models\Support;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
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


        return view('frontend.movies.home')->with('all_support',$all_support)
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
            $output = view("frontend.movies.list_mobile_2columns_cell",compact('movies','type'))->render();
        }
        return Response(array('total'=>$total,'nb'=>sizeof($movies),'content'=>$output));
    }
}