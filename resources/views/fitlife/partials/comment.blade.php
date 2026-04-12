<div class="ns-comment" style="{{ isset($isReply) ? 'margin-left: 40px; border-left: 2px solid #edeff1;' : '' }}">
    <div class="comment-avatar">{{ strtoupper(substr($comment->username, 0, 1)) }}</div>
    <div class="comment-right">
        <div class="comment-meta">
            <span class="comment-username">u/{{ $comment->username }}</span>
            <span class="comment-time">· {{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <div class="comment-body">{{ $comment->content }}</div>
        <div class="comment-actions" style="margin-top: 8px;">
            <button onclick="openCommentModal({{ $comment->id }}, '{{ $comment->username }}')" class="ns-action" style="padding: 2px 6px; font-size: 1.1rem;">
                <ion-icon name="chatbubble-outline"></ion-icon> Reply
            </button>
        </div>

        @if($comment->replies && $comment->replies->count() > 0)
            <div class="replies-toggle" style="margin-top: 8px;">
                <button onclick="toggleReplies({{ $comment->id }})" class="ns-action" style="padding: 2px 6px; font-size: 1.1rem; color: #ff4500;">
                    <ion-icon name="chevron-down-outline" id="replies-icon-{{ $comment->id }}"></ion-icon> 
                    {{ $comment->replies->count() }} {{ Str::plural('Reply', $comment->replies->count()) }}
                </button>
            </div>
            <div class="replies-thread" id="replies-{{ $comment->id }}" style="margin-top: 15px; display: none;">
                @foreach($comment->replies as $reply)
                    @include('fitlife.partials.comment', ['comment' => $reply, 'isReply' => true])
                @endforeach
            </div>
        @endif
    </div>
</div>
