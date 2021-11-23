<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    //Transform the resource into an array.
    //param  \Illuminate\Http\Request  $request
    //return array
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'body' => $this->body,
            'attachment' => $this->attachment,
            'comments_count' => $this->when(isset($this->comments_count), $this->comments_count)
        ];
    }
}
