<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CourseComment;

class CourseComments extends Component
{
    public $courseSlug;
    public $newComment = '';
    public $replyComment = '';
    public $replyingTo = null;
    public $editingCommentId = null;
    public $editingCommentText = '';

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
            'user_id' => auth()->check() ? auth()->id() : null,
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest Student',
            'avatar' => auth()->check() 
                ? (auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name))
                : 'https://i.pravatar.cc/150?u=' . session()->getId(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
    }

    public function setReply($id)
    {
        $this->replyingTo = $id;
        $this->replyComment = '';
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyComment = '';
    }

    public function submitReply()
    {
        $this->validate([
            'replyComment' => 'required|string|max:1000',
        ]);

        CourseComment::create([
            'course_slug' => $this->courseSlug,
            'parent_id' => $this->replyingTo,
            'user_id' => auth()->check() ? auth()->id() : null,
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest Student',
            'avatar' => auth()->check() 
                ? (auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name))
                : 'https://i.pravatar.cc/150?u=' . session()->getId(),
            'content' => $this->replyComment,
        ]);

        $this->replyComment = '';
        $this->replyingTo = null;
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

    public function startEditComment($id)
    {
        $comment = CourseComment::findOrFail($id);
        if ($comment->user_id !== auth()->id()) return;

        // 5 minute window
        if ($comment->created_at->diffInMinutes() >= 5) {
            $this->dispatch('notify', ['message' => 'Time limit for editing (5m) has passed.', 'type' => 'error']);
            return;
        }

        $this->editingCommentId = $id;
        $this->editingCommentText = $comment->content;
    }

    public function cancelEditComment()
    {
        $this->editingCommentId = null;
        $this->editingCommentText = '';
    }

    public function updateComment()
    {
        if (!$this->editingCommentId) return;

        $comment = CourseComment::findOrFail($this->editingCommentId);
        if ($comment->user_id !== auth()->id()) return;

        if ($comment->created_at->diffInMinutes() >= 5) {
            $this->dispatch('notify', ['message' => 'Time limit for editing (5m) has passed.', 'type' => 'error']);
            $this->cancelEditComment();
            return;
        }

        $this->validate([
            'editingCommentText' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $this->editingCommentText,
        ]);

        $this->cancelEditComment();
    }

    public function deleteComment($id)
    {
        $comment = CourseComment::findOrFail($id);
        if ($comment->user_id !== auth()->id()) return;

        // Delete replies too
        $comment->replies()->delete();
        $comment->delete();
        
        $this->dispatch('comment-deleted');
    }

    public function render()
    {
        $comments = CourseComment::where('course_slug', $this->courseSlug)
            ->whereNull('parent_id')
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.course-comments', compact('comments'));
    }
}
