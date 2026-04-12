<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsPost extends Model
{
    protected $guarded = [];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function comments()
    {
        return $this->hasMany(NewsComment::class);
    }

    public function votes()
    {
        return $this->hasMany(NewsVote::class);
    }
}
