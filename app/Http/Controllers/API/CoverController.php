<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CoverController extends Controller
{
   public function proxy($isbn)
{
    $isbn = preg_replace('/[^0-9X]/', '', $isbn);
    $url = "https://couverture.geobib.fr/api/v1/{$isbn}/medium";

    try {
        $response = Http::timeout(5)->withoutVerifying()->get($url);

        Log::info("BNF COVER URL", ['url' => $url, 'status' => $response->status()]);

        $imageData = $response->body();

        if (!$response->successful() || strlen($imageData) < 500) {
            return response()->json(['error' => 'Pas de couverture BnF'], 404);
        }

        return Response::make($imageData, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Length' => strlen($imageData),
            'Access-Control-Allow-Origin' => '*',
        ]);
    } catch (\Exception $e) {
        Log::error("Erreur proxy couverture BNF: {$e->getMessage()}");
        return response()->json(['error' => 'Erreur serveur'], 500);
    }
}

}