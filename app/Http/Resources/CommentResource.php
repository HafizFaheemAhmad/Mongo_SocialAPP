<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    //Transform the resource into an array.
    //param  \Illuminate\Http\Request  $request
    //return array
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id,
            'post_id' =>  $this->post->id,
            'comment' => $this->comment,
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
        ];
    }
}
