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

    protected $hidden = [
        'liked_by',
        'user_id',
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

    public function isLikedBy($identifier)
    {
        return is_array($this->liked_by) && in_array($identifier, $this->liked_by);
    }
}
