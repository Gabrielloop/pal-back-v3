<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogUserCrud
{
    public function handle(Request $request, Closure $next)
    {

    // Avant la requête
    Log::info(' Avant la requête : ' . $request->method() . ' ' . $request->path());

    // Appelle la suite (le conbtroleur ou le middleware suivant)
    $response = $next($request);

    // Après la requête
    Log::info(' Après la requête : ' . $response->status());

   return $response;

    }

}
