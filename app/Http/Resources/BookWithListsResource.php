<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookWithListsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'book'  => new BookResource($this['book']),
            'lists' => UserlistResource::collection($this['lists']),
        ];
    }
}
