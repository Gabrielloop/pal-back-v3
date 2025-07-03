<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\BookCacheService;
use App\Models\Book;
use App\Services\BnfService;

class BnfProxyController extends Controller
{
    protected BnfService $bnf;

    public function __construct(BnfService $bnf)
    {
        $this->bnf = $bnf;
    }

    public function proxy(Request $request)
    {
        $query = trim($request->input('query', ''));
        $page = max((int) $request->input('page', 1), 1);

        try {
            $books = $this->bnf->search($query, $page);

            return response()->json([
                'success' => true,
                'data' => $books
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur BnF',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
