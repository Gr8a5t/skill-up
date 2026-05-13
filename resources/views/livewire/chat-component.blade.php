<div class="chat-layout" x-data="{ mobileView: '{{ $activeRecipientHash ? 'feed' : 'list' }}' }">
    <!-- Left Column: Conversations List -->
    <aside class="chat-side" :class="{ 'mobile-hidden': mobileView === 'feed' }">
        <header class="list-header">
            <h1 class="chat-title">Chats</h1>
            <div class="header-actions">
                <div class="search-wrap" style="position: relative; flex-grow: 1; margin-right: 15px;">
                    <ion-icon name="search-outline" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #888;"></ion-icon>
                    <input type="text" wire:model.live="search" placeholder="Search chats..." style="width: 100%; padding: 8px 10px 8px 35px; border: 1px solid #eee; border-radius: 20px; font-size: 1.3rem; outline: none; background: #f9f9f9;">
                </div>
            </div>
        </header>

        <div class="conversations-list">
            @forelse($conversations as $chat)
            <a href="#" wire:click.prevent="selectRecipient('{{ $chat['id'] }}')" @click="mobileView = 'feed'" class="chat-item {{ $chat['unread'] ? 'unread' : '' }} {{ $activeRecipientHash == $chat['id'] ? 'active' : '' }}">
                <div style="position: relative;">
                    <div class="chat-avatar">
                        <img src="{{ $chat['avatar'] }}" alt="{{ $chat['name'] }}">
                    </div>
                    @if($chat['is_online'])
                        <div style="position: absolute; bottom: 0; right: 0; width: 14px; height: 14px; background: #23a55a; border-radius: 50%; border: 2.5px solid #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.1);"></div>
                    @endif
                </div>
                <div class="chat-content">
                    <div class="chat-meta">
                        <span class="chat-name">{{ $chat['name'] }}</span>
                        <span style="color: var(--text-mut); font-size: 1rem;">•</span>
                        <span class="chat-time">{{ $chat['time'] }}</span>
                    </div>
                    <div class="chat-preview">{{ \Str::limit($chat['message'], 40) }}</div>
                </div>
                @if($chat['unread'])
                <div class="unread-dot"></div>
                @endif
            </a>
            @empty
            <div style="padding: 40px; text-align: center; color: #888;">
                <ion-icon name="chatbubbles-outline" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.5;"></ion-icon>
                <p style="font-size: 1.3rem;">No conversations yet.</p>
            </div>
            @endforelse
        </div>
    </aside>

    <!-- Right Column: Chat Feed -->
    <main class="chat-main" :class="{ 'mobile-hidden': mobileView === 'list' }">
        @if($activeRecipient)
        <header class="feed-header">
            <div class="feed-user">
                <button @click="mobileView = 'list'" class="back-btn" style="display: none; background: none; border: none; font-size: 2rem; color: var(--brand-primary); margin-right: 10px; cursor: pointer;">
                    <span wire:ignore style="display: flex; align-items: center;"><ion-icon name="chevron-back"></ion-icon></span>
                </button>
                <div class="chat-avatar" style="width: 40px; height: 40px;">
                    <img src="{{ $activeRecipient->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($activeRecipient->name) . '&background=f0ebff&color=8e54e9' }}" alt="Active Chat">
                </div>
                <div>
                    <div class="feed-user-name">{{ $activeRecipient->name }}</div>
                    <div class="feed-status">
                        @if($activeRecipient->isOnline())
                            <div class="status-dot" style="background: #23a55a;"></div> Active now
                        @else
                            <div class="status-dot" style="background: #aaa;"></div> Offline
                        @endif
                    </div>
                </div>
            </div>

        </header>

        <div class="feed-content" id="chat-feed" wire:poll.5s="markAsRead">
            @foreach($messages as $m)
            <div class="msg-wrapper" 
                x-data="{ 
                    swiped: false, 
                    startX: 0, 
                    currentX: 0,
                    isMe: {{ $m->sender_id === auth()->id() ? 'true' : 'false' }},
                    canEdit: {{ $m->sender_id === auth()->id() && $m->created_at->diffInMinutes() < 5 ? 'true' : 'false' }},
                    handleTouchStart(e) {
                        if (!this.isMe) return;
                        this.startX = e.touches[0].clientX;
                    },
                    handleTouchMove(e) {
                        if (!this.isMe) return;
                        let diff = e.touches[0].clientX - this.startX;
                        if (diff < 0 && diff > -100) {
                            this.currentX = diff;
                        }
                    },
                    handleTouchEnd() {
                        if (!this.isMe) return;
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
                @dblclick="if(isMe) { swiped = !swiped; currentX = swiped ? -80 : 0; }"
                @click.away="resetSwipe()"
                style="user-select: none;"
            >
                <div class="msg-line {{ $m->sender_id === auth()->id() ? 'me' : 'other' }}" 
                    :style="`transform: translateX(${currentX}px)`"
                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;"
                >
                    @if($m->sender_id === auth()->id())
                    <div class="msg-actions me" :class="{ 'visible': swiped }">
                        @if($m->created_at->diffInMinutes() < 5)
                        <button class="action-btn edit" title="Edit" wire:click="startEditMessage({{ $m->id }})">
                            <span wire:ignore><ion-icon name="create-outline"></ion-icon></span>
                        </button>
                        @endif
                        <button class="action-btn delete" title="Delete" onclick="confirm('Delete this message?') || event.stopImmediatePropagation()" wire:click="deleteMessage({{ $m->id }})">
                            <span wire:ignore><ion-icon name="trash-outline"></ion-icon></span>
                        </button>
                    </div>
                    @endif

                    @if($m->sender_id !== auth()->id())
                    <div class="msg-actions other" :class="{ 'visible': swiped }">
                         <!-- Other user messages usually don't have edit/delete for current user -->
                    </div>
                    @endif

                    @if($editingMessageId === $m->id)
                        <div class="msg-bubble editing">
                            <textarea wire:model="editingMessageText" class="edit-input" autofocus @keydown.enter.prevent="$wire.updateMessage()" @keydown.escape.prevent="$wire.cancelEditMessage()"></textarea>
                            <div class="edit-actions">
                                <button type="button" wire:click="updateMessage()" class="save-btn"><ion-icon name="checkmark-circle"></ion-icon></button>
                                <button type="button" wire:click="cancelEditMessage()" class="cancel-btn"><ion-icon name="close-circle"></ion-icon></button>
                            </div>
                        </div>
                    @else
                        <div class="msg-bubble">
                            {!! preg_replace('~(https?://[^\s]+)~', '<a href="$1" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline; font-weight: bold;">$1</a>', e($m->message)) !!}
                            @if($m->updated_at->diffInSeconds($m->created_at) > 0)
                                <span class="edited-label">(edited)</span>
                            @endif
                        </div>
                    @endif
                    <div class="msg-time">{{ $m->created_at->format('g:i A') }}</div>
                </div>
            </div>
            @endforeach
            <div id="scroll-anchor"></div>
        </div>

        <form wire:submit.prevent="sendMessage" class="feed-input-area">
            <div class="feed-input-wrapper">
                <div wire:ignore style="display: flex; align-items: center;">
                    <ion-icon name="add-outline" style="font-size: 2.2rem; color: var(--text-mut); cursor: pointer;"></ion-icon>
                </div>
                <input type="text" wire:model="newMessage" class="feed-input" placeholder="Type a message...">
                <button type="submit" class="feed-btn" @if(empty($newMessage)) disabled style="opacity: 0.3; cursor: default;" @endif>
                    <span wire:ignore style="display: flex; align-items: center; justify-content: center;"><ion-icon name="paper-plane"></ion-icon></span>
                </button>
            </div>
        </form>
        @else
        <div style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #888; background: #fdfdfd;">
            <div style="width: 200px; height: 200px; background: #f0f2f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <ion-icon name="chatbubbles" style="font-size: 8rem; color: #ccc;"></ion-icon>
            </div>
            <h2 style="font-size: 2.2rem; color: #333; margin-bottom: 10px;">Your Messages</h2>
            <p style="font-size: 1.5rem; text-align: center; max-width: 300px;">Select a person from the left to start a conversation.</p>
        </div>
        @endif
    </main>

    <style>
        @media (max-width: 992px) {
            .chat-side.mobile-hidden, .chat-main.mobile-hidden { display: none !important; }
            .back-btn { display: block !important; }
            .chat-side { width: 100% !important; border-right: none !important; }
            .chat-main { width: 100% !important; }
        }
        
        /* Auto-scroll helper */
        #chat-feed { scroll-behavior: smooth; }

        .msg-wrapper { position: relative; margin-bottom: 8px; display: flex; flex-direction: column; width: 100%; overflow: hidden; }
        
        .msg-actions { 
            position: absolute; top: 0; bottom: 0; 
            display: flex; gap: 8px; align-items: center; opacity: 0; pointer-events: none; transition: all 0.3s ease; 
            z-index: 1;
        }
        .msg-actions.me { right: -80px; } /* Positioned to the right of the message */
        .msg-actions.other { right: -80px; } 
        
        .msg-actions.visible { opacity: 1; pointer-events: auto; transform: translateX(-80px); }
        
        .action-btn { 
            width: 32px; height: 32px; border-radius: 50%; border: none; 
            display: flex; align-items: center; justify-content: center; font-size: 1.6rem; cursor: pointer;
            transition: transform 0.2s;
        }
        .action-btn:active { transform: scale(0.9); }
        .action-btn.edit { background: #f0f2f5; color: var(--brand-primary); }
        .action-btn.delete { background: #ffebee; color: #ff5252; }
        
        .msg-line { position: relative; z-index: 2; max-width: 85% !important; }
        .msg-line.me { align-self: flex-end; align-items: flex-end; }
        .msg-line.other { align-self: flex-start; align-items: flex-start; }
        
        .msg-bubble { position: relative; }
        .msg-bubble.editing { 
            background: #fff !important; color: #333 !important; border: 2px solid var(--brand-primary); 
            padding: 8px !important; min-width: 200px; max-width: 100%;
        }
        .edit-input { 
            width: 100%; min-height: 40px; border: none; outline: none; font-family: inherit; font-size: 1.3rem; 
            resize: none; background: transparent;
        }
        .edit-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 5px; }
        .edit-actions button { 
            background: none; border: none; font-size: 2rem; cursor: pointer; display: flex; align-items: center; 
        }
        .save-btn { color: #23a55a; }
        .cancel-btn { color: #ff5252; }
        
        .edited-label { font-size: 0.9rem; opacity: 0.6; margin-left: 5px; font-style: italic; }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
             const scrollToBottom = () => {
                const feed = document.getElementById('chat-feed');
                if (feed) {
                    feed.scrollTop = feed.scrollHeight;
                }
            };
            
            scrollToBottom();
            
            Livewire.on('message-sent', () => {
                setTimeout(scrollToBottom, 100);
            });
            
            // Also scroll when poll updates messages
            Livewire.on('refreshMessages', () => {
                setTimeout(scrollToBottom, 100);
            });
        });
    </script>
</div>
