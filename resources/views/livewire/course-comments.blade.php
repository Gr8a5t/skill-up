<div wire:poll.3s>
    <section class="content-section">
        <h2 class="section-label">Discussion ({{ $comments->count() }})</h2>
        
        <form wire:submit="submitComment" class="comment-input-area">
            <img src="{{ auth()->check() ? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) : 'https://i.pravatar.cc/150?u=' . session()->getId() }}" class="comment-avatar" alt="You">
            <input type="text" wire:model="newComment" placeholder="Ask a question or share a thought..." required style="flex:1;">
            <button type="submit">Post</button>
        </form>

        @forelse($comments as $comment)
        <div class="comment-box" wire:key="comment-{{ $comment->id }}">
            <img src="{{ $comment->avatar }}" class="comment-avatar" alt="User">
            <div class="comment-content" style="width: 100%;">
                <h5>{{ $comment->user_name }} <span>• {{ $comment->created_at->diffForHumans() }}</span></h5>
                <p>{{ $comment->content }}</p>
                <div class="comment-actions">
                    @php
                        $userIdentifier = auth()->check() ? auth()->id() : session()->getId();
                        $hasLiked = is_array($comment->liked_by) && in_array($userIdentifier, $comment->liked_by);
                    @endphp
                    <button wire:click="likeComment({{ $comment->id }})" class="{{ $hasLiked ? 'liked' : '' }}" style="display:flex; align-items:center; gap:4px; {{ $hasLiked ? 'color: var(--brand-primary);' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                        </svg>
                        {{ $comment->likes }}
                    </button>
                    <button>Reply</button>
                </div>
            </div>
        </div>
        @empty
        <div style="color: #888; font-style: italic; margin-top:20px;">No comments yet. Be the first to start the discussion!</div>
        @endforelse
    </section>
</div>
