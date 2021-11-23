<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'comment', 'user_id', 'parent_id', 'commentable_id', 'commentable_type', 'post_id'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
