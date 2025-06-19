<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'isbn'           => $this->isbn,
            'book_title'     => $this->book_title,
            'book_author'    => $this->book_author,
            'book_publisher' => $this->book_publisher,
            'book_year'      => $this->book_year,
        ];
    }
}
