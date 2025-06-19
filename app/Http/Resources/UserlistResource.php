<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserlistResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'userlist_id'          => $this->userlist_id,
            'userlist_name'        => $this->userlist_name,
            'userlist_description' => $this->userlist_description,
            'userlist_type'        => $this->userlist_type,
        ];
    }
}
