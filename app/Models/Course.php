<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category', 'level', 'icon', 'color',
        'playlist_id', 'recap', 'key_concepts',
        'source_files_url', 'cheatsheet_url', 'is_published',
    ];

    protected $casts = [
        'key_concepts' => 'array',
        'is_published'  => 'boolean',
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
}
