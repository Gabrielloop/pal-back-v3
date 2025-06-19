<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\BookCacheService;


class BnfProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $query = trim($request->input('query', ''));
        $page = max((int) $request->input('page', 1), 1);
        $isIsbn = preg_match('/^(?:\d{9}[\dX]|\d{13})$/', $query);

        $finalQuery = $isIsbn
            ? 'isbn="' . $query . '"'
            : 'bib.doctype all "a" AND title="' . $query . '" AND bib.language any "fre"';

        $params = [
            'version' => '1.2',
            'operation' => 'searchRetrieve',
            'query' => $finalQuery,
            'startRecord' => 1 + ($page - 1) * 10,
            'maximumRecords' => $isIsbn ? 1 : 10,
            'recordSchema' => 'dc',
        ];

        try {
            $response = Http::withoutVerifying()->get('https://catalogue.bnf.fr/api/SRU', $params);
            $xml = simplexml_load_string($response->body());

            $xml->registerXPathNamespace('srw', 'http://www.loc.gov/zing/srw/');
            $records = $xml->xpath('//srw:record');

            $books = [];

            foreach ($records as $record) {
                $dc = $record->children('http://www.loc.gov/zing/srw/')
                             ->recordData
                             ->children('http://www.openarchives.org/OAI/2.0/oai_dc/')
                             ->dc;

                if (!$dc) continue;

                $book = [
                    'title'      => $this->clean((string) $dc->children('http://purl.org/dc/elements/1.1/')->title),
                    'isbn'      =>   $this->extractISBN($dc->children('http://purl.org/dc/elements/1.1/')->identifier),
                    'author'   => $this->clean((string) $dc->children('http://purl.org/dc/elements/1.1/')->creator),
                    'year'       => (string) $dc->children('http://purl.org/dc/elements/1.1/')->date ?: 'Date inconnue',
                    'publisher'  => $this->clean((string) $dc->children('http://purl.org/dc/elements/1.1/')->publisher),
                    // 'docType'    => (string) $dc->children('http://purl.org/dc/elements/1.1/')->type ?? null,
                ];

                if (!empty($book['isbn']) && $book['isbn'] !== 'ISBN inconnu') {
                    BookCacheService::store($book);
                }

                $books[] = $book;
            }

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

    private function extractISBN($identifiers)
    {
        foreach ((array) $identifiers as $id) {
            if (str_contains($id, 'ISBN')) {
                return trim(str_replace('ISBN', '', $id));
            }
        }
        return 'ISBN inconnu';
    }

    private function clean($text)
    {
        $text = preg_replace('/\(.*?\)|\s\/.*$|\s;.*$/', '', $text);
        $text = preg_replace('/\. Auteur du texte/', '', $text);
        return trim($text);
    }
}
