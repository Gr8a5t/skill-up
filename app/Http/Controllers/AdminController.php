<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Lesson;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function courses()
    {
        $courses = Course::withCount('lessons')->latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        return view('admin.courses.create');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string',
            'level'    => 'required|in:Beginner,Medium,Advance',
            'icon'     => 'nullable|string',
            'color'    => 'nullable|string',
            'playlist_id'   => 'nullable|string',
            'recap'         => 'nullable|string',
            'key_concepts'  => 'nullable|string',
            'source_files_url' => 'nullable|url',
            'cheatsheet_url'   => 'nullable|url',
            'lessons'          => 'nullable|array',
            'lessons.*.title'    => 'required_with:lessons|string',
            'lessons.*.video_id' => 'required_with:lessons|string',
        ]);

        $keyConceptsArray = null;
        if ($request->filled('key_concepts')) {
            $keyConceptsArray = array_values(
                array_filter(array_map('trim', explode("\n", $request->key_concepts)))
            );
        }

        $course = Course::create([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'category'    => $request->category,
            'level'       => $request->level,
            'icon'        => $request->icon ?? 'book-outline',
            'color'       => $request->color ?? '#f5f5f5',
            'playlist_id' => $request->playlist_id,
            'recap'       => $request->recap,
            'key_concepts' => $keyConceptsArray,
            'source_files_url' => $request->source_files_url,
            'cheatsheet_url'   => $request->cheatsheet_url,
            'is_published'     => $request->has('is_published'),
        ]);

        // Save lessons
        if ($request->has('lessons')) {
            foreach ($request->lessons as $idx => $lessonData) {
                if (!empty($lessonData['title']) && !empty($lessonData['video_id'])) {
                    Lesson::create([
                        'course_id' => $course->id,
                        'title'     => $lessonData['title'],
                        'video_id'  => $lessonData['video_id'],
                        'order'     => $idx,
                    ]);
                }
            }
        }

        return redirect()->route('admin.courses')->with('success', 'Course "' . $course->title . '" created successfully!');
    }

    public function editCourse(Course $course)
    {
        $course->load('lessons');
        return view('admin.courses.edit', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string',
            'level'    => 'required|in:Beginner,Medium,Advance',
        ]);

        $keyConceptsArray = null;
        if ($request->filled('key_concepts')) {
            $keyConceptsArray = array_values(
                array_filter(array_map('trim', explode("\n", $request->key_concepts)))
            );
        }

        $course->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title),
            'category'    => $request->category,
            'level'       => $request->level,
            'icon'        => $request->icon ?? $course->icon,
            'color'       => $request->color ?? $course->color,
            'playlist_id' => $request->playlist_id,
            'recap'       => $request->recap,
            'key_concepts' => $keyConceptsArray,
            'source_files_url' => $request->source_files_url,
            'cheatsheet_url'   => $request->cheatsheet_url,
            'is_published'     => $request->has('is_published'),
        ]);

        // Sync lessons: delete old, re-insert
        $course->lessons()->delete();
        if ($request->has('lessons')) {
            foreach ($request->lessons as $idx => $lessonData) {
                if (!empty($lessonData['title']) && !empty($lessonData['video_id'])) {
                    Lesson::create([
                        'course_id' => $course->id,
                        'title'     => $lessonData['title'],
                        'video_id'  => $lessonData['video_id'],
                        'order'     => $idx,
                    ]);
                }
            }
        }

        return redirect()->route('admin.courses')->with('success', 'Course updated successfully!');
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }
}
