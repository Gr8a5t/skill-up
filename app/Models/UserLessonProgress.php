<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'user_lesson_progress';

    protected $fillable = [
        'user_id',
        'session_id',
        'course_slug',
        'video_id',
        'progress_seconds',
        'total_seconds',
        'is_completed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
