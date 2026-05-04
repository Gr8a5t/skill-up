<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CourseComment;

class CourseComments extends Component
{
    public $courseSlug;
    public $newComment = '';

    public function mount($courseSlug)
    {
        $this->courseSlug = $courseSlug;
    }

    public function submitComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        CourseComment::create([
            'course_slug' => $this->courseSlug,
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest Student',
            'avatar' => auth()->check() 
                ? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) 
                : 'https://i.pravatar.cc/150?u=' . session()->getId(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
    }

    public function likeComment($id)
    {
        $comment = CourseComment::find($id);
        if ($comment) {
            $identifier = auth()->check() ? auth()->id() : session()->getId();
            // Handle Laravel's json casting and ensuring it's an array
            $likedBy = is_array($comment->liked_by) ? $comment->liked_by : json_decode($comment->liked_by ?? '[]', true);
            
            if (!in_array($identifier, $likedBy)) {
                $likedBy[] = $identifier;
                $comment->liked_by = $likedBy;
                $comment->likes++;
            } else {
                $likedBy = array_diff($likedBy, [$identifier]);
                // Re-index array because array_diff preserves keys
                $comment->liked_by = array_values($likedBy);
                $comment->likes = max(0, $comment->likes - 1);
            }
            $comment->save();
        }
    }

    public function render()
    {
        $comments = CourseComment::where('course_slug', $this->courseSlug)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.course-comments', compact('comments'));
    }
}
