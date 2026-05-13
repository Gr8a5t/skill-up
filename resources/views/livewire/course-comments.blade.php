<div wire:poll.3s>
    <section class="content-section">
        <h2 class="section-label">Discussion ({{ $comments->count() }})</h2>
        
        <form wire:submit="submitComment" class="comment-input-area">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f0ebff&color=8e54e9' }}" class="comment-avatar" alt="You">
            <input type="text" wire:model="newComment" placeholder="Ask a question or share a thought..." required style="flex:1;">
            <button type="submit">Post</button>
        </form>

        @forelse($comments as $comment)
        <div class="comment-wrapper"
            x-data="{ 
                swiped: false, 
                startX: 0, 
                currentX: 0,
                isOwner: {{ auth()->check() && $comment->user_id === auth()->id() ? 'true' : 'false' }},
                handleTouchStart(e) {
                    if (!this.isOwner) return;
                    this.startX = e.touches[0].clientX;
                },
                handleTouchMove(e) {
                    if (!this.isOwner) return;
                    let diff = e.touches[0].clientX - this.startX;
                    if (diff < 0 && diff > -100) {
                        this.currentX = diff;
                    }
                },
                handleTouchEnd() {
                    if (!this.isOwner) return;
                    if (this.currentX < -50) {
                        this.swiped = true;
                        this.currentX = -80;
                    } else {
                        this.swiped = false;
                        this.currentX = 0;
                    }
                },
                resetSwipe() {
                    this.swiped = false;
                    this.currentX = 0;
                }
            }"
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
            @dblclick="if(isOwner) { swiped = !swiped; currentX = swiped ? -80 : 0; }"
            @click.away="resetSwipe()"
            style="user-select: none;"
        >
            @if(auth()->check() && $comment->user_id === auth()->id())
            <div class="comment-reveal-actions" :class="{ 'visible': swiped }">
                @if($comment->created_at->diffInMinutes() < 5)
                <button class="action-btn edit" title="Edit" wire:click="startEditComment({{ $comment->id }})">
                    <span wire:ignore>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                    </span>
                </button>
                @endif
                <button class="action-btn delete" title="Delete" onclick="confirm('Delete this comment and all its replies?') || event.stopImmediatePropagation()" wire:click="deleteComment({{ $comment->id }})">
                    <span wire:ignore>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </span>
                </button>
            </div>
            @endif

            <div class="comment-box" wire:key="comment-{{ $comment->id }}" :style="`transform: translateX(${currentX}px)`" style="transition: transform 0.3s ease;">
                <img src="{{ $comment->avatar }}" class="comment-avatar" alt="User">
                <div class="comment-content" style="width: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            @if($comment->user_id)
                                <a href="{{ route('profile.show', \App\Utils\HashId::encode($comment->user_id)) }}" style="text-decoration:none; color:inherit; font-weight:600;">{{ $comment->user_name }}</a>
                            @else
                                {{ $comment->user_name }}
                            @endif
                            <span style="font-size: 0.85rem; color: #999; margin-left: 5px;">• {{ $comment->created_at->diffForHumans() }}</span>
                            @if($comment->updated_at->diffInSeconds($comment->created_at) > 0)
                                <span style="font-size: 0.8rem; color: #aaa; font-style: italic; margin-left: 5px;">(edited)</span>
                            @endif
                        </div>
                    </div>

                    @if($editingCommentId === $comment->id)
                        <div class="edit-comment-area" style="margin-top: 10px;">
                            <textarea wire:model="editingCommentText" class="comment-edit-input" autofocus></textarea>
                            <div style="display: flex; gap: 10px; margin-top: 5px;">
                                <button wire:click="updateComment" style="padding: 4px 12px; background: var(--brand-primary); color: #fff; border: none; border-radius: 4px; cursor: pointer;">Save</button>
                                <button wire:click="cancelEditComment" style="padding: 4px 12px; background: #eee; color: #333; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                            </div>
                        </div>
                    @else
                        <p style="margin-top: 5px; font-size: 1.2rem; color: #444;">{{ $comment->content }}</p>
                    @endif

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
                    <div class="comment-wrapper" 
                        x-data="{ 
                            swiped: false, 
                            startX: 0, 
                            currentX: 0,
                            isOwner: {{ auth()->check() && $reply->user_id === auth()->id() ? 'true' : 'false' }},
                            handleTouchStart(e) {
                                if (!this.isOwner) return;
                                this.startX = e.touches[0].clientX;
                            },
                            handleTouchMove(e) {
                                if (!this.isOwner) return;
                                let diff = e.touches[0].clientX - this.startX;
                                if (diff < 0 && diff > -100) {
                                    this.currentX = diff;
                                }
                            },
                            handleTouchEnd() {
                                if (!this.isOwner) return;
                                if (this.currentX < -50) {
                                    this.swiped = true;
                                    this.currentX = -80;
                                } else {
                                    this.swiped = false;
                                    this.currentX = 0;
                                }
                            },
                            resetSwipe() {
                                this.swiped = false;
                                this.currentX = 0;
                            }
                        }"
                        @touchstart="handleTouchStart"
                        @touchmove="handleTouchMove"
                        @touchend="handleTouchEnd"
                        @dblclick="if(isOwner) { swiped = !swiped; currentX = swiped ? -80 : 0; }"
                        @click.away="resetSwipe()"
                        style="position: relative; user-select: none;"
                    >
                        @if(auth()->check() && $reply->user_id === auth()->id())
                        <div class="comment-reveal-actions" :class="{ 'visible': swiped }">
                            @if($reply->created_at->diffInMinutes() < 5)
                            <button class="action-btn edit" title="Edit" wire:click="startEditComment({{ $reply->id }})">
                                <span wire:ignore>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                                </span>
                            </button>
                            @endif
                            <button class="action-btn delete" title="Delete" onclick="confirm('Delete this reply?') || event.stopImmediatePropagation()" wire:click="deleteComment({{ $reply->id }})">
                                <span wire:ignore>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </span>
                            </button>
                        </div>
                        @endif

                        <div class="comment-box" wire:key="reply-{{ $reply->id }}" style="margin-bottom: 12px; padding-bottom: 0; border: none; gap:10px; transition: transform 0.3s ease;" :style="`transform: translateX(${currentX}px)`">
                            <img src="{{ $reply->avatar }}" class="comment-avatar" alt="User" style="width:34px; height:34px;">
                            <div class="comment-content" style="width: 100%;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        @if($reply->user_id)
                                            <a href="{{ route('profile.show', \App\Utils\HashId::encode($reply->user_id)) }}" style="text-decoration:none; color:inherit; font-weight:600;">{{ $reply->user_name }}</a>
                                        @else
                                            {{ $reply->user_name }}
                                        @endif
                                        <span style="font-size:0.85rem; color: #999; margin-left: 5px;">• {{ $reply->created_at->diffForHumans() }}</span>
                                        @if($reply->updated_at->diffInSeconds($reply->created_at) > 0)
                                            <span style="font-size: 0.8rem; color: #aaa; font-style: italic; margin-left: 5px;">(edited)</span>
                                        @endif
                                    </div>
                                </div>

                                @if($editingCommentId === $reply->id)
                                    <div class="edit-comment-area" style="margin-top: 5px;">
                                        <textarea wire:model="editingCommentText" class="comment-edit-input small" autofocus></textarea>
                                        <div style="display: flex; gap: 8px; margin-top: 5px;">
                                            <button wire:click="updateComment" style="padding: 2px 10px; background: var(--brand-primary); color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Save</button>
                                            <button wire:click="cancelEditComment" style="padding: 2px 10px; background: #eee; color: #333; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Cancel</button>
                                        </div>
                                    </div>
                                @else
                                    <p style="font-size:1.15rem; color:#555; margin-top: 2px;">{{ $reply->content }}</p>
                                @endif
                            </div>
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

    <style>
        .comment-wrapper { position: relative; margin-bottom: 15px; overflow: visible; }
        .comment-reveal-actions { 
            position: absolute; right: 0; top: 0; bottom: 0; 
            display: flex; gap: 8px; align-items: center; padding-right: 10px; opacity: 0; pointer-events: none; transition: all 0.3s ease;
        }
        .comment-reveal-actions.visible { opacity: 1; pointer-events: auto; transform: translateX(-80px); }
        
        .action-btn { 
            width: 36px; height: 36px; border-radius: 50%; border: none; 
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: transform 0.2s;
        }
        .action-btn:active { transform: scale(0.9); }
        .action-btn.edit { background: #f0ebff; color: var(--brand-primary); }
        .action-btn.delete { background: #ffebee; color: #ff5252; }
        
        .comment-edit-input { 
            width: 100%; min-height: 60px; padding: 10px; border: 1px solid var(--brand-primary); border-radius: 6px; 
            outline: none; font-family: inherit; font-size: 1.15rem; resize: vertical; background: #fff;
        }
        .comment-edit-input.small { min-height: 40px; font-size: 1rem; }
    </style>
</div>
