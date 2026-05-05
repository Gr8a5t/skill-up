<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseComment extends Model
{
    protected $fillable = [
        'course_slug',
        'user_id',
        'user_name',
        'avatar',
        'content',
        'parent_id',
        'likes',
        'liked_by',
    ];

    protected $casts = [
        'liked_by' => 'array',
    ];

    public function replies()
    {
        return $this->hasMany(CourseComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
