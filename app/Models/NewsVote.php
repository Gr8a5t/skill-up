<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsVote extends Model
{
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(NewsPost::class, 'news_post_id');
    }
}
