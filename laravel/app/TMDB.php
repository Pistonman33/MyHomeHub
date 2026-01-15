<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class TMDB extends Model
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('THEMOVIEDB_API_KEY'); 
    }

    public function searchByName($name)
    {
        // Requête à l'API TMDB
        $response = Http::get("https://api.themoviedb.org/3/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $name,
            'language' => 'fr-FR', 
        ]);

        if ($response->successful()) {
            return $response->json()["results"];
        }

        return false; 
    }

    public function getMovieDetails($movieId)
    {
        $response = Http::get("https://api.themoviedb.org/3/movie/{$movieId}", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'append_to_response' => 'credits',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return false; 
    }
    public function searchSerieByName($name)
    {
        // Requête à l'API TMDB
        $response = Http::get("https://api.themoviedb.org/3/search/tv", [
            'api_key' => $this->apiKey,
            'query' => $name,
            'language' => 'fr-FR', 
        ]);

        if ($response->successful()) {
            return $response->json()["results"];
        }

        return false; 
    }

    public function getSerieDetails($movieId)
    {
        $response = Http::get("https://api.themoviedb.org/3/tv/{$movieId}", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'append_to_response' => 'credits',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return false; 
    }    
}
