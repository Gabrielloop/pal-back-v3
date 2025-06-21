<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'isbn'           => $this->isbn,
            'title'     => $this->title,
            'author'    => $this->author,
            'publisher' => $this->publisher,
            'year'      => $this->year,
        ];
    }
}
