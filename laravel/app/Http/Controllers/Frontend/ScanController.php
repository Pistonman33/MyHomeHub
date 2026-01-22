<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ScanController extends Controller
{
  public function home(){
    return view('frontend.library.scan.home');
  }

 public function lookup(Request $request)
    {
        $barcode = $request->input('code');

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun code barre fourni'
            ], 400);
        }

        // CALL UPCitemdb (FREE TRIAL)
        $url = "https://api.upcitemdb.com/prod/trial/lookup";

        $response = Http::get($url, [
            'upc' => $barcode
        ]);

        // Si erreur HTTP
        if (!$response->ok()) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur API UPCitemdb',
                'status' => $response->status()
            ], 500);
        }

        $data = $response->json();

        // Vérifier si produit trouvé
        if (empty($data['items']) || !isset($data['items'][0])) {
            $isbnResponse = Http::get("https://www.googleapis.com/books/v1/volumes", [
                'q' => "isbn:" . $barcode
            ]);

            if ($isbnResponse->ok()) {
                $bookData = $isbnResponse->json();
                if (!empty($bookData['items'])) {
                    Log::info("[ScanController] - Google response: \r\n" . print_r($bookData, true));
                    $info = $bookData['items'][0]['volumeInfo'];
                    $searchinfo = $bookData['items'][0]['searchInfo'];
                    $result['title'] = $info['title'] ?? null;
                    $result['category'] = "Books";
                    $result['description'] = $searchinfo['textSnippet'] ?? null;
                    $result['image'] = $info['imageLinks']['thumbnail'] ?? null;
                    return response()->json([
                      'success' => true,
                      'product' => $result
                    ]);
                }else{
                  return response()->json([
                      'success' => false,
                      'error' => 'Produit non trouvé dans UPCitemdb et google apis Books',
                  ]);
                }
            }else{
            return response()->json([
                'success' => false,
                'error' => 'Erreur API Google Books',
                'status' => $isbnResponse->status()
            ], 500);
            }
        }

        $item = $data['items'][0];

        // Construire résultats simples
        $result = [
            'title' => $item['title'] ?? null,
            'ean' => $item['ean'] ?? null,
            'brand' => $item['brand'] ?? null,
            'description' => $item['description'] ?? null,
            'image' => $item['images'][0] ?? null,
            'category' => $item['category'] ?? null,
            'lowest_recorded_price' => $item['lowest_recorded_price'] ?? null,
            'highest_recorded_price' => $item['highest_recorded_price'] ?? null,
        ];

        return response()->json([
            'success' => true,
            'product' => $result
        ]);
    }

}
