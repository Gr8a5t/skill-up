<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Auth;

class ForumComponent extends Component
{
    public $body = '';
    public $activeTab = 'For you';
    
    public $topics = [
        'For you', 'Topics', 'Web Designer', 'UI Designer', '#frontenddevelopment'
    ];

    protected $rules = [
        'body' => 'required|min:3|max:2000',
    ];

    public function createPost()
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        ForumPost::create([
            'user_id' => Auth::id(),
            'body' => $this->body,
            'tags' => [$this->activeTab === 'For you' ? 'General' : $this->activeTab],
        ]);

        $this->body = '';
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $posts = ForumPost::with('user')->latest()->get();

        return view('livewire.forum-component', [
            'posts' => $posts
        ]);
    }
}
