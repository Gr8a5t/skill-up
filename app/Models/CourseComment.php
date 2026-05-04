<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseComment extends Model
{
    protected $fillable = [
        'course_slug',
        'user_name',
        'avatar',
        'content',
        'likes',
        'liked_by',
    ];

    protected $casts = [
        'liked_by' => 'array',
    ];
}
