<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsPost;
use App\Models\Community;
use App\Models\NewsComment;
use App\Models\NewsVote;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    private function getSessionId(Request $request)
    {
        if (!$request->session()->has('news_session_id')) {
            $request->session()->put('news_session_id', Str::uuid()->toString());
        }
        return $request->session()->get('news_session_id');
    }

    public function index(Request $request)
    {
        $sort = $request->query('sort', 'hot'); // hot, new, top

        $query = NewsPost::with('community');

        if ($sort === 'new') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'top') {
            $query->orderBy('votes_count', 'desc');
        } else {
            // "hot" can just be recent and high votes, simplify to votes + recent
            $query->orderBy('votes_count', 'desc')->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(20);
        $communities = Community::all();

        return view('fitlife.news', compact('posts', 'communities', 'sort'));
    }

    public function show(NewsPost $post)
    {
        $post->load(['community', 'comments' => function($q) {
            $q->whereNull('parent_id')->with('replies')->orderBy('created_at', 'asc');
        }]);
        $communities = Community::all();
        
        return view('fitlife.news-show', compact('post', 'communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'community_id' => 'required|exists:communities,id'
        ]);

        NewsPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'community_id' => $request->community_id,
            'session_id' => $this->getSessionId($request),
            'username' => auth()->user()->name
        ]);

        return redirect()->route('news.index')->with('success', 'Post created successfully!');
    }

    public function comment(Request $request, NewsPost $post)
    {
        $request->validate([
            'content' => 'required',
            'parent_id' => 'nullable|exists:news_comments,id'
        ]);

        $post->comments()->create([
            'content' => $request->content,
            'session_id' => $this->getSessionId($request),
            'username' => auth()->user()->name,
            'parent_id' => $request->parent_id
        ]);

        $post->increment('comments_count');

        return back()->with('success', 'Comment added!');
    }

    public function vote(Request $request, NewsPost $post)
    {
        $request->validate(['vote' => 'required|in:1,-1']);
        
        $sessionId = $this->getSessionId($request);
        $voteValue = (int) $request->vote;

        $existingVote = NewsVote::where('news_post_id', $post->id)
                                ->where('session_id', $sessionId)
                                ->first();

        if ($existingVote) {
             if ($existingVote->vote_type === $voteValue) {
                  // remove vote if clicking same one again
                  $existingVote->delete();
                  $post->decrement('votes_count', $voteValue);
                  return response()->json(['success' => true, 'action' => 'removed', 'new_score' => $post->votes_count]);
             } else {
                  // switch vote
                  $existingVote->update(['vote_type' => $voteValue]);
                  // If it was 1, and now -1, diff is -2.
                  $post->increment('votes_count', $voteValue * 2);
                  return response()->json(['success' => true, 'action' => 'switched', 'new_score' => $post->votes_count]);
             }
        }

        // New Vote
        NewsVote::create([
            'news_post_id' => $post->id,
            'session_id' => $sessionId,
            'vote_type' => $voteValue
        ]);
        
        $post->increment('votes_count', $voteValue);

        return response()->json(['success' => true, 'action' => 'added', 'new_score' => $post->votes_count]);
    }
}
