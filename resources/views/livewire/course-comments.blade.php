<div wire:poll.3s>
    <section class="content-section">
        <h2 class="section-label">Discussion ({{ $comments->count() }})</h2>
        
        <form wire:submit="submitComment" class="comment-input-area">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f0ebff&color=8e54e9' }}" class="comment-avatar" alt="You">
            <input type="text" wire:model="newComment" placeholder="Ask a question or share a thought..." required style="flex:1;">
            <button type="submit">Post</button>
        </form>

        @forelse($comments as $comment)
        <div class="comment-box" wire:key="comment-{{ $comment->id }}">
            <img src="{{ $comment->avatar }}" class="comment-avatar" alt="User">
            <div class="comment-content" style="width: 100%;">
                            @if($comment->user_id)
                                <a href="{{ route('profile.show', \App\Utils\HashId::encode($comment->user_id)) }}" style="text-decoration:none; color:inherit;">{{ $comment->user_name }}</a>
                            @else
                                {{ $comment->user_name }}
                            @endif
                             <span style="font-size: 0.85rem; color: #999; margin-left: 5px;">• {{ $comment->created_at->diffForHumans() }}</span>
                <p style="margin-top: 5px;">{{ $comment->content }}</p>
                <div class="comment-actions">
                    @php
                        $userIdentifier = auth()->check() ? auth()->id() : session()->getId();
                        $hasLiked = $comment->isLikedBy($userIdentifier);
                    @endphp
                    <button wire:click="likeComment({{ $comment->id }})" class="{{ $hasLiked ? 'liked' : '' }}" style="display:flex; align-items:center; gap:4px; {{ $hasLiked ? 'color: var(--brand-primary);' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                        </svg>
                        {{ $comment->likes }}
                    </button>
                    <button wire:click="setReply({{ $comment->id }})">Reply</button>
                </div>

                @if($replyingTo === $comment->id)
                <div class="reply-form" style="margin-top: 10px; display:flex; gap:10px;">
                    <input type="text" wire:model="replyComment" placeholder="Write a reply..." style="flex:1; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:1.15rem; outline:none;">
                    <button wire:click="submitReply" style="padding:6px 14px; background:var(--brand-primary); color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Post Reply</button>
                    <button wire:click="cancelReply" style="padding:6px 14px; background:#f5f5f5; color:#555; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Cancel</button>
                </div>
                @endif
                
                @if($comment->replies->count() > 0)
                <div class="replies" style="margin-top: 16px; padding-left: 20px; border-left: 2px solid #eaeaea;">
                    @foreach($comment->replies as $reply)
                    <div class="comment-box" wire:key="reply-{{ $reply->id }}" style="margin-bottom: 12px; padding-bottom: 0; border: none; gap:10px;">
                        <img src="{{ $reply->avatar }}" class="comment-avatar" alt="User" style="width:34px; height:34px;">
                        <div class="comment-content" style="width: 100%;">
                            @if($reply->user_id)
                                <a href="{{ route('profile.show', \App\Utils\HashId::encode($reply->user_id)) }}" style="text-decoration:none; color:inherit;">{{ $reply->user_name }}</a>
                            @else
                                {{ $reply->user_name }}
                            @endif
                             <span style="font-size:0.85rem; color: #999;">• {{ $reply->created_at->diffForHumans() }}</span>
                            <p style="font-size:1.15rem; color:#555; margin-top: 2px;">{{ $reply->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div style="color: #888; font-style: italic; margin-top:20px;">No comments yet. Be the first to start the discussion!</div>
        @endforelse
    </section>
</div>
