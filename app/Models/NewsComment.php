<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsComment extends Model
{
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(NewsPost::class, 'news_post_id');
    }

    public function replies()
    {
        return $this->hasMany(NewsComment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewsComment::class, 'parent_id');
    }
}
