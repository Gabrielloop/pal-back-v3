<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Book;

class BookCacheService
{
    public static function getByIsbn(string $isbn): ?array
    {
        return Cache::get(self::cacheKey($isbn));
    }

    public static function store(array $book): void
    {
        if (isset($book['isbn'])) {
            Cache::put(self::cacheKey($book['isbn']), $book, now()->addDay());
        }
    }

    public static function ensurePersisted(string $isbn): ?Book
    {
        $book = Book::where('isbn', $isbn)->first();
        if ($book) {
            return $book;
        }

        $cached = self::getByIsbn($isbn);
        if (!$cached) {
            return null;
        }

        return Book::create([
            'isbn' => $isbn,
            'title' => $cached['title'] ?? 'Titre inconnu',
            'author' => $cached['author'] ?? 'Auteur inconnu',
            'publisher' => $cached['publisher'] ?? 'Éditeur inconnu',
            'year' => $cached['year'] ?? 'Année inconnue',
        ]);
    }

    private static function cacheKey(string $isbn): string
    {
        return 'isbn_' . $isbn;
    }
}
