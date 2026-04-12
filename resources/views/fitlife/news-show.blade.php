@extends('layouts.fitlife')

@section('title', $post->title . ' | SkillUp Community')

@section('content')

<style>
/* ===== NEWS SHOW PAGE ===== */
.ns-wrap {
    padding-top: 90px;
    padding-bottom: 80px;
    min-height: 100vh;
    background: #f6f7f8;
    font-family: var(--ff-rubik);
}

.ns-inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 20px;
}

.ns-back {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 1.4rem; font-weight: 700; color: #878a8c;
    margin-bottom: 16px; text-decoration: none; transition: 0.15s;
}
.ns-back:hover { color: #ff4500; }

/* Layout */
.ns-layout {
    display: grid;
    grid-template-columns: 1fr 312px;
    gap: 24px;
    align-items: start;
}

/* Post card */
.ns-post-card {
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    display: flex;
    overflow: hidden;
    margin-bottom: 10px;
}

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
.vbtn:hover { color: #ff4500; }
.vbtn.voted-up { color: #ff4500; }
.vbtn.voted-down { color: #7193ff; }
.vscore {
    font-size: 1.2rem; font-weight: 700; color: #1c1c1c;
    line-height: 1; text-align: center;
}

.ns-post-body { padding: 12px 16px; flex-grow: 1; }

.ns-post-meta {
    display: flex; gap: 8px; align-items: center;
    flex-wrap: wrap; margin-bottom: 8px;
}
.ns-community {
    font-size: 1.2rem; font-weight: 700; color: #1c1c1c; text-decoration: none;
}
.ns-community:hover { text-decoration: underline; }
.ns-dot { color: #ccc; font-size: 1rem; }
.ns-author { font-size: 1.2rem; color: #878a8c; }

.ns-post-title {
    font-size: 2.2rem; font-weight: 600; color: #222;
    line-height: 1.3; margin-bottom: 14px;
}

.ns-post-content {
    font-size: 1.5rem; color: #3c3c3c; line-height: 1.7;
    margin-bottom: 16px; white-space: pre-wrap;
}

.ns-post-actions {
    display: flex; gap: 4px; padding-top: 8px;
    border-top: 1px solid #f1f1f1;
}
.ns-action {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 1.2rem; font-weight: 700; color: #878a8c;
    padding: 6px 8px; border-radius: 2px;
    text-decoration: none; background: none; border: none;
    cursor: pointer; transition: 0.15s;
}
.ns-action:hover { background: #f6f7f8; color: #1c1c1c; }

/* Comment Box */
.ns-comment-box {
    background: #fff; border: 1px solid #ccc;
    border-radius: 4px; padding: 16px; margin-bottom: 10px;
}
.ns-comment-box p {
    font-size: 1.4rem; color: #1c1c1c; margin-bottom: 12px;
}
.ns-comment-box p span { color: #ff4500; font-weight: 700; }
.ns-textarea {
    width: 100%; padding: 12px 14px;
    font-size: 1.4rem; font-family: inherit;
    border: 1px solid #edeff1; border-radius: 4px;
    background: #f6f7f8; resize: vertical; min-height: 100px;
    transition: border-color 0.2s, background 0.2s; color: #1c1c1c;
    display: block; margin-bottom: 10px;
}
.ns-textarea:focus { border-color: #ff4500; outline: none; background: #fff; box-shadow: 0 0 0 3px rgba(255,69,0,0.08); }
.ns-submit {
    float: right;
    padding: 8px 22px; border-radius: 20px;
    background: #ff4500; color: #fff;
    font-size: 1.3rem; font-weight: 700;
    border: none; cursor: pointer; transition: 0.15s;
}
.ns-submit:hover { background: #e03d00; }
.ns-form-footer { display: flex; justify-content: flex-end; }

/* Comment count header */
.ns-comments-header {
    background: #fff; border: 1px solid #ccc;
    border-radius: 4px 4px 0 0;
    padding: 12px 16px;
    font-size: 1.5rem; font-weight: 700; color: #1c1c1c;
    border-bottom: 1px solid #f1f1f1;
}

/* Single comment */
.ns-comment-thread {
    background: #fff; border: 1px solid #ccc;
    border-top: none; border-radius: 0 0 4px 4px;
}
.ns-comment {
    display: flex; gap: 12px;
    padding: 16px;
    border-bottom: 1px solid #f1f1f1;
}
.ns-comment:last-child { border-bottom: none; }

.comment-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #0079d3, #46d0eb);
    color: #fff; font-size: 1.3rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.comment-right { flex-grow: 1; }
.comment-meta {
    display: flex; gap: 8px; align-items: center;
    margin-bottom: 6px; flex-wrap: wrap;
}
.comment-username { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; }
.comment-time { font-size: 1.2rem; color: #878a8c; }
.comment-body { font-size: 1.4rem; color: #3c3c3c; line-height: 1.6; }

/* Empty comments */
.ns-empty-comments {
    background: #fff; border: 1px solid #ccc;
    border-top: none; border-radius: 0 0 4px 4px;
    padding: 40px 20px; text-align: center;
    font-size: 1.4rem; color: #878a8c;
}

/* Sidebar */
.ns-sidebar { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 95px; }

.s-box { background: #fff; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; }
.s-box-header {
    background: linear-gradient(135deg, #ff4500, #ff6534);
    color: #fff; font-size: 1.4rem; font-weight: 700; padding: 12px 16px;
}
.s-box-body { padding: 16px; }
.s-stat { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f1f1; font-size: 1.3rem; }
.s-stat:last-child { border-bottom: none; }
.s-stat-label { color: #878a8c; }
.s-stat-value { font-weight: 700; color: #1c1c1c; }
.s-back-btn {
    display: flex; justify-content: center; align-items: center;
    width: 100%; padding: 10px; border-radius: 20px;
    background: #ff4500; color: #fff; margin-top: 14px;
    font-size: 1.4rem; font-weight: 700; border: none;
    cursor: pointer; text-decoration: none; transition: 0.15s;
}
.s-back-btn:hover { background: #e03d00; color: #fff; }

/* Alert */
.n-alert { border-radius: 4px; padding: 12px 16px; font-size: 1.4rem; font-weight: 500; margin-bottom: 12px; }
.n-alert-success { background: #e6f4ea; color: #1e7e34; border: 1px solid #b7dfbf; }

/* Responsive */
@media (max-width: 960px) {
    .ns-layout { grid-template-columns: 1fr; }
    .ns-sidebar { display: none; }
}
@media (max-width: 480px) {
    .ns-post-title { font-size: 1.8rem; }
    .ns-post-content { font-size: 1.4rem; }
    .ns-comment { padding: 12px; }
}

/* Mobile floating comment button */
.mobile-comment-fab {
    display: none;
    position: fixed; bottom: 24px; right: 24px;
    background: #ff4500; color: #fff;
    border: none; border-radius: 50px;
    padding: 12px 20px; font-size: 1.4rem; font-weight: 700;
    cursor: pointer; z-index: 100;
    align-items: center; gap: 8px;
    box-shadow: 0 4px 15px rgba(255,69,0,0.4);
    transition: 0.2s;
}
.mobile-comment-fab:hover { background: #e03d00; }
@media (max-width: 960px) {
    .mobile-comment-fab { display: flex; }
}
</style>

<div class="ns-wrap">
    <div class="ns-inner">

        <a href="{{ route('news.index') }}" class="ns-back">
            <ion-icon name="arrow-back-outline"></ion-icon> Back to Community
        </a>

        <div class="ns-layout">

            {{-- Main Column --}}
            <div>
                @if(session('success'))
                    <div class="n-alert n-alert-success">{{ session('success') }}</div>
                @endif

                {{-- Post --}}
                <div class="ns-post-card">
                    <div class="vote-rail">
                        <button class="vbtn upvote" data-post-id="{{ $post->id }}" data-vote="1" title="Upvote">
                            <ion-icon name="caret-up"></ion-icon>
                        </button>
                        <span class="vscore" id="score-{{ $post->id }}">{{ $post->votes_count }}</span>
                        <button class="vbtn downvote" data-post-id="{{ $post->id }}" data-vote="-1" title="Downvote">
                            <ion-icon name="caret-down"></ion-icon>
                        </button>
                    </div>

                    <div class="ns-post-body">
                        <div class="ns-post-meta">
                            @if($post->community)
                                <a href="#" class="ns-community">s/{{ $post->community->name }}</a>
                                <span class="ns-dot">•</span>
                            @endif
                            <span class="ns-author">Posted by u/{{ $post->username }} · {{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <h1 class="ns-post-title">{{ $post->title }}</h1>

                        <div class="ns-post-content">{{ $post->content }}</div>

                        <div class="ns-post-actions">
                            <span class="ns-action">
                                <ion-icon name="chatbubble-outline"></ion-icon>
                                {{ $post->comments_count }} {{ Str::plural('Comment', $post->comments_count) }}
                            </span>
                            <button class="ns-action">
                                <ion-icon name="share-social-outline"></ion-icon> Share
                            </button>
                            <button class="ns-action">
                                <ion-icon name="bookmark-outline"></ion-icon> Save
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Comments --}}
                <div class="ns-comments-header">
                    {{ $post->comments_count }} {{ Str::plural('Comment', $post->comments_count) }}
                </div>

                @if($post->comments->isNotEmpty())
                <div class="ns-comment-thread">
                    @foreach($post->comments as $comment)
                        @include('fitlife.partials.comment', ['comment' => $comment])
                    @endforeach
                </div>
                @else
                <div class="ns-empty-comments">
                    No comments yet — start the conversation!
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="ns-sidebar">
                <div class="s-box">
                    <div class="s-box-header">About this Post</div>
                    <div class="s-box-body">
                        <div class="s-stat">
                            <span class="s-stat-label">Community</span>
                            <span class="s-stat-value">{{ $post->community?->name ?? 'General' }}</span>
                        </div>
                        <div class="s-stat">
                            <span class="s-stat-label">Posted by</span>
                            <span class="s-stat-value">{{ $post->username }}</span>
                        </div>
                        <div class="s-stat">
                            <span class="s-stat-label">Votes</span>
                            <span class="s-stat-value">{{ $post->votes_count }}</span>
                        </div>
                        <div class="s-stat">
                            <span class="s-stat-label">Comments</span>
                            <span class="s-stat-value">{{ $post->comments_count }}</span>
                        </div>
                        <div class="s-stat">
                            <span class="s-stat-label">Posted</span>
                            <span class="s-stat-value">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                        <button onclick="openCommentModal()" class="s-back-btn" style="margin-top: 14px; background: #ff4500;">
                            <ion-icon name="chatbubble-ellipses-outline"></ion-icon> Leave a Comment
                        </button>
                        <a href="{{ route('news.index') }}" class="s-back-btn" style="margin-top: 8px; background: transparent; color: #878a8c; border: 1px solid #ccc;">← Back to Feed</a>
                    </div>
                </div>

                @if($communities->isNotEmpty())
                <div class="s-box">
                    <div class="s-box-header">Other Communities</div>
                    <div class="s-box-body">
                        @foreach($communities as $community)
                        <a href="{{ route('news.index') }}" class="d-flex align-items-center" style="text-decoration: none; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f1f1f1; display: flex;">
                            <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#ff4500,#ff6534); color:#fff; font-size:1.2rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink: 0;">
                                {{ strtoupper(substr($community->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:1.3rem; font-weight:700; color:#1c1c1c;">s/{{ $community->name }}</div>
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

{{-- Mobile floating comment button (shown when sidebar is hidden) --}}
<button class="mobile-comment-fab" onclick="openCommentModal()">
    <ion-icon name="chatbubble-ellipses-outline"></ion-icon> Comment
</button>

<script>
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
    });
});

function openCommentModal(parentId = null, username = null) {
    document.getElementById('modalParentId').value = parentId || '';
    const targetText = document.getElementById('commentTarget');
    if (parentId && username) {
        targetText.innerHTML = `Replying to <strong style="color:#ff4500;">u/${username}</strong>`;
    } else {
        targetText.innerHTML = `Commenting as <strong style="color:#ff4500;">{{ auth()->user()->name }}</strong>`;
    }
    document.getElementById('commentModal').classList.remove('hidden');
}

function toggleReplies(commentId) {
    const el = document.getElementById('replies-' + commentId);
    const icon = document.getElementById('replies-icon-' + commentId);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        if (icon) icon.setAttribute('name', 'chevron-up-outline');
    } else {
        el.style.display = 'none';
        if (icon) icon.setAttribute('name', 'chevron-down-outline');
    }
}
</script>

{{-- Comment Modal --}}
<div id="commentModal" class="n-modal-wrap hidden">
    <div class="n-modal">
        <div class="n-modal-head">
            <h3>Leave a Comment</h3>
            <button class="n-modal-close" onclick="document.getElementById('commentModal').classList.add('hidden')">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>
        <div class="n-modal-body">
            <p id="commentTarget" style="font-size:1.4rem; color:#878a8c; margin-bottom:14px;">Commenting as <strong style="color:#ff4500;">{{ auth()->user()->name }}</strong></p>
            <form action="{{ route('news.comment', $post->id) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" id="modalParentId" value="">
                <textarea name="content" class="ns-textarea" placeholder="What are your thoughts?..." required></textarea>
                <div class="ns-form-footer">
                    <button type="submit" class="ns-submit">Post Comment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.n-modal-wrap { position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(3px); }
.n-modal-wrap.hidden { display: none; }
.n-modal { background: #fff; border-radius: 8px; width: 100%; max-width: 580px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
.n-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #edeff1; }
.n-modal-head h3 { font-size: 1.8rem; font-weight: 700; color: #1c1c1c; margin: 0; }
.n-modal-close { background: none; border: none; font-size: 2.2rem; cursor: pointer; color: #878a8c; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.15s; }
.n-modal-close:hover { background: #f6f7f8; color: #1c1c1c; }
.n-modal-body { padding: 24px; }
</style>

@endsection
