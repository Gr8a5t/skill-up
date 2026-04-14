@extends('layouts.fitlife')

@section('title', 'Community News & Discussions | SkillUp')

@section('content')

<style>
/* ===== NEWS PAGE STYLES ===== */
.news-page-wrap {
    padding-top: 90px;
    padding-bottom: 80px;
    min-height: 100vh;
    background: #f6f7f8;
    font-family: var(--ff-rubik);
}

.news-inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Sort + Create bar */
.news-bar {
    background: #fff;
    border: 1px solid #e4e4e4;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    margin-bottom: 20px;
}
.news-bar-tabs { display: flex; align-items: center; gap: 4px; }
.news-sort-tab {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 1.4rem; font-weight: 700; color: #878a8c;
    padding: 8px 14px; border-radius: 20px;
    text-decoration: none; transition: 0.15s;
    border: none; background: none; cursor: pointer;
}
.news-sort-tab:hover { background: #f6f7f8; color: #0f1a1c; }
.news-sort-tab.active { color: #ff4500; background: rgba(255,69,0,0.08); }

.btn-create-post {
    display: inline-flex; align-items: center; gap: 6px;
    background: #ff4500; color: #fff;
    font-size: 1.3rem; font-weight: 700; padding: 7px 18px;
    border-radius: 20px; border: none; cursor: pointer;
    transition: background 0.15s; text-decoration: none; white-space: nowrap;
}
.btn-create-post:hover { background: #e03d00; color: #fff; }

/* 2 col layout */
.news-layout {
    display: grid;
    grid-template-columns: 1fr 312px;
    gap: 24px;
    align-items: start;
}

/* Feed */
.news-feed { display: flex; flex-direction: column; gap: 10px; }

/* Card */
.news-card {
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    display: flex;
    align-items: stretch;
    overflow: hidden;
    transition: border-color 0.15s;
    cursor: pointer;
}
.news-card:hover { border-color: #898989; }

.vote-rail {
    background: #f8f9fa;
    width: 40px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 4px;
    gap: 2px;
}
.vbtn {
    background: none; border: none; cursor: pointer;
    color: #878a8c; font-size: 1.8rem; padding: 2px;
    line-height: 1; border-radius: 2px; transition: 0.15s;
    display: flex; align-items: center; justify-content: center;
}
.vbtn:hover { color: #ff4500; background: rgba(255,69,0,0.06); }
.vbtn.voted-up { color: #ff4500; }
.vbtn.voted-down { color: #7193ff; }
.vscore {
    font-size: 1.2rem; font-weight: 700; color: #1c1c1c;
    line-height: 1; text-align: center;
}

.card-body {
    padding: 8px 10px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-meta {
    display: flex; gap: 8px; align-items: center;
    flex-wrap: wrap; margin-bottom: 6px;
}
.card-community {
    font-size: 1.2rem; font-weight: 700; color: #1c1c1c;
    text-decoration: none;
}
.card-community:hover { text-decoration: underline; }
.card-dot { color: #ccc; font-size: 1rem; }
.card-author { font-size: 1.2rem; color: #878a8c; }

.card-title {
    font-size: 1.8rem; font-weight: 500; color: #222;
    line-height: 1.3; margin-bottom: 8px;
    text-decoration: none; display: block;
}
.card-title:hover { color: #0079d3; }

.card-footer {
    display: flex; gap: 4px; align-items: center; flex-wrap: wrap;
}
.card-action {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 1.2rem; font-weight: 700; color: #878a8c;
    padding: 6px 8px; border-radius: 2px;
    text-decoration: none; background: none; border: none;
    cursor: pointer; transition: 0.15s;
}
.card-action:hover { background: #f6f7f8; color: #1c1c1c; }

/* Empty */
.empty-feed {
    background: #fff; border: 1px solid #ccc;
    border-radius: 4px; padding: 60px 20px; text-align: center;
}
.empty-feed h3 { font-size: 2rem; color: #1c1c1c; margin-bottom: 8px; }
.empty-feed p { font-size: 1.4rem; color: #878a8c; }

/* ===== SIDEBAR ===== */
.news-sidebar { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 95px; }

.s-box {
    background: #fff; border: 1px solid #ccc;
    border-radius: 4px; overflow: hidden;
}
.s-box-header {
    background: #ff4500; color: #fff;
    font-size: 1.4rem; font-weight: 700;
    padding: 12px 16px;
}
.s-box-body { padding: 16px; }

.s-desc { font-size: 1.4rem; color: #1c1c1c; line-height: 1.5; margin-bottom: 16px; }
.s-create-btn {
    display: flex; justify-content: center; align-items: center;
    width: 100%; padding: 10px; border-radius: 20px;
    background: #ff4500; color: #fff;
    font-size: 1.4rem; font-weight: 700; border: none;
    cursor: pointer; transition: 0.15s; text-decoration: none;
}
.s-create-btn:hover { background: #e03d00; color: #fff; }

.comm-row {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid #f1f1f1;
    text-decoration: none;
}
.comm-row:last-child { border-bottom: none; }
.comm-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, #ff4500, #ff6534);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; font-weight: 700; flex-shrink: 0;
}
.comm-name { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; }
.comm-members { font-size: 1.1rem; color: #878a8c; }

/* ===== MODAL ===== */
.n-modal-wrap {
    position: fixed; inset: 0; background: rgba(0,0,0,0.55);
    z-index: 2000; display: flex; align-items: center; justify-content: center;
    padding: 20px; backdrop-filter: blur(3px);
}
.n-modal-wrap.hidden { display: none; }
.n-modal {
    background: #fff; border-radius: 8px; width: 100%;
    max-width: 600px; overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
.n-modal-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 24px; border-bottom: 1px solid #edeff1;
}
.n-modal-head h3 { font-size: 1.8rem; font-weight: 700; color: #1c1c1c; margin: 0; }
.n-modal-close {
    background: none; border: none; font-size: 2.2rem; cursor: pointer;
    color: #878a8c; width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; transition: 0.15s;
}
.n-modal-close:hover { background: #f6f7f8; color: #1c1c1c; }
.n-modal-body { padding: 24px; }
.n-form-group { margin-bottom: 16px; }
.n-form-label { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; display: block; margin-bottom: 6px; }
.n-form-ctrl {
    width: 100%; padding: 12px 14px;
    font-size: 1.4rem; font-family: inherit;
    border: 1px solid #edeff1; border-radius: 4px;
    transition: border-color 0.2s; background: #f6f7f8; color: #1c1c1c;
}
.n-form-ctrl:focus { border-color: #ff4500; outline: none; background: #fff; box-shadow: 0 0 0 3px rgba(255,69,0,0.1); }
.n-submit-btn {
    width: 100%; padding: 13px; border-radius: 20px;
    background: #ff4500; color: #fff; font-size: 1.5rem; font-weight: 700;
    border: none; cursor: pointer; transition: 0.15s;
}
.n-submit-btn:hover { background: #e03d00; }

/* Alert */
.n-alert { border-radius: 4px; padding: 12px 16px; font-size: 1.4rem; font-weight: 500; margin-bottom: 16px; }
.n-alert-success { background: #e6f4ea; color: #1e7e34; border: 1px solid #b7dfbf; }

/* Responsive */
@media (max-width: 960px) {
    .news-layout { grid-template-columns: 1fr; }
    .news-sidebar { display: none; }
    .news-bar { flex-wrap: wrap; gap: 10px; }
}
@media (max-width: 480px) {
    .news-bar-tabs { gap: 0; }
    .news-sort-tab { padding: 7px 10px; font-size: 1.3rem; }
    .card-title { font-size: 1.6rem; }
    .vscore { font-size: 1.1rem; }
}
</style>

<div class="news-page-wrap">
    <div class="news-inner">

        {{-- Top bar --}}
        <div class="news-bar">
            <div class="news-bar-tabs">
                <a href="?sort=hot" class="news-sort-tab {{ $sort === 'hot' ? 'active' : '' }}">
                    <ion-icon name="flame-outline"></ion-icon> Hot
                </a>
                <a href="?sort=new" class="news-sort-tab {{ $sort === 'new' ? 'active' : '' }}">
                    <ion-icon name="sparkles-outline"></ion-icon> New
                </a>
                <a href="?sort=top" class="news-sort-tab {{ $sort === 'top' ? 'active' : '' }}">
                    <ion-icon name="trophy-outline"></ion-icon> Top
                </a>
            </div>
            <button class="btn-create-post" onclick="document.getElementById('newPostModal').classList.remove('hidden')">
                <ion-icon name="add-circle-outline"></ion-icon> Create Post
            </button>
        </div>

        {{-- Main layout --}}
        <div class="news-layout">
            {{-- Feed --}}
            <div class="news-feed">

                @forelse($posts as $post)
                <div class="news-card" onclick="window.location='{{ route('news.show', $post->id) }}'">
                    {{-- Vote rail --}}
                    <div class="vote-rail" onclick="event.stopPropagation()">
                        <button class="vbtn upvote" data-post-id="{{ $post->id }}" data-vote="1" title="Upvote">
                            <ion-icon name="caret-up"></ion-icon>
                        </button>
                        <span class="vscore" id="score-{{ $post->id }}">{{ $post->votes_count }}</span>
                        <button class="vbtn downvote" data-post-id="{{ $post->id }}" data-vote="-1" title="Downvote">
                            <ion-icon name="caret-down"></ion-icon>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="card-body">
                        <div class="card-meta">
                            @if($post->community)
                                <a href="#" class="card-community" onclick="event.stopPropagation()">s/{{ $post->community->name }}</a>
                                <span class="card-dot">•</span>
                            @endif
                            <span class="card-author">Posted by u/{{ $post->username }} · {{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <a href="{{ route('news.show', $post->id) }}" class="card-title" onclick="event.stopPropagation()">
                            {{ $post->title }}
                        </a>

                        <div class="card-footer" onclick="event.stopPropagation()">
                            <a href="{{ route('news.show', $post->id) }}" class="card-action">
                                <ion-icon name="chatbubble-outline"></ion-icon>
                                {{ $post->comments_count }} {{ Str::plural('Comment', $post->comments_count) }}
                            </a>
                            <button class="card-action">
                                <ion-icon name="share-social-outline"></ion-icon> Share
                            </button>
                            <button class="card-action">
                                <ion-icon name="bookmark-outline"></ion-icon> Save
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-feed">
                    <h3>No posts yet</h3>
                    <p>Be the first to start a discussion in the SkillUp community!</p>
                </div>
                @endforelse

                <div>{{ $posts->links() }}</div>
            </div>

            {{-- Sidebar --}}
            <aside class="news-sidebar">
                <div class="s-box">
                    <div class="s-box-header">SkillUp Community</div>
                    <div class="s-box-body">
                        <p class="s-desc">A place to share knowledge, ask questions, and connect with fellow learners on your tech journey.</p>
                        <button class="s-create-btn" onclick="document.getElementById('newPostModal').classList.remove('hidden')">
                            <ion-icon name="add-circle-outline"></ion-icon> Create Post
                        </button>
                    </div>
                </div>

                @if($communities->isNotEmpty())
                <div class="s-box">
                    <div class="s-box-header">Communities</div>
                    <div class="s-box-body">
                        @foreach($communities as $community)
                        <a href="#" class="comm-row">
                            <div class="comm-avatar">{{ strtoupper(substr($community->name, 0, 1)) }}</div>
                            <div>
                                <div class="comm-name">s/{{ $community->name }}</div>
                                <div class="comm-members">{{ rand(10, 500) }} members</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</div>

{{-- Create Post Modal --}}
<div id="newPostModal" class="n-modal-wrap hidden">
    <div class="n-modal">
        <div class="n-modal-head">
            <h3>Create a Post</h3>
            <button class="n-modal-close" onclick="document.getElementById('newPostModal').classList.add('hidden')">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <div class="n-modal-body">
            <form action="{{ route('news.store') }}" method="POST">
                @csrf
                <div class="n-form-group">
                    <label class="n-form-label">Title</label>
                    <input type="text" name="title" placeholder="An interesting title..." required class="n-form-ctrl">
                </div>
                <div class="n-form-group">
                    <label class="n-form-label">Content</label>
                    <textarea name="content" rows="5" placeholder="What are your thoughts?" required class="n-form-ctrl" style="resize: vertical;"></textarea>
                </div>
                <button type="submit" class="n-submit-btn">Post</button>
            </form>
        </div>
    </div>
</div>

<script>
// AJAX Voting
document.querySelectorAll('.vbtn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const vote = this.dataset.vote;

        fetch(`/news/${postId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ vote: parseInt(vote) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('score-' + postId).innerText = data.new_score;
                const rail = this.closest('.vote-rail');
                rail.querySelectorAll('.vbtn').forEach(b => b.classList.remove('voted-up', 'voted-down'));
                if (data.action !== 'removed') {
                    this.classList.add(parseInt(vote) === 1 ? 'voted-up' : 'voted-down');
                }
            }
        });
</script>

@endsection
