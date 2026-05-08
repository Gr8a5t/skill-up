<div class="chat-layout" x-data="{ mobileView: '{{ $activeRecipientId ? 'feed' : 'list' }}' }">
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
            <a href="#" wire:click.prevent="selectRecipient({{ $chat['id'] }})" @click="mobileView = 'feed'" class="chat-item {{ $chat['unread'] ? 'unread' : '' }} {{ $activeRecipientId == $chat['id'] ? 'active' : '' }}">
                <div class="chat-avatar">
                    <img src="{{ $chat['avatar'] }}" alt="{{ $chat['name'] }}">
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
                    <ion-icon name="chevron-back"></ion-icon>
                </button>
                <div class="chat-avatar" style="width: 40px; height: 40px;">
                    <img src="{{ $activeRecipient->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($activeRecipient->name) . '&background=f0ebff&color=8e54e9' }}" alt="Active Chat">
                </div>
                <div>
                    <div class="feed-user-name">{{ $activeRecipient->name }}</div>
                    <div class="feed-status"><div class="status-dot"></div> Active now</div>
                </div>
            </div>
            <div class="feed-actions">
                <ion-icon name="call-outline" style="font-size: 2rem; color: var(--text-mut); cursor: pointer;"></ion-icon>
                <ion-icon name="videocam-outline" style="font-size: 2.2rem; color: var(--text-mut); cursor: pointer; margin-left: 15px;"></ion-icon>
            </div>
        </header>

        <div class="feed-content" id="chat-feed" wire:poll.5s="markAsRead">
            @foreach($messages as $m)
            <div class="msg-line {{ $m->sender_id === auth()->id() ? 'me' : 'other' }}">
                <div class="msg-bubble">{{ $m->message }}</div>
                <div class="msg-time">{{ $m->created_at->format('g:i A') }}</div>
            </div>
            @endforeach
            <div id="scroll-anchor"></div>
        </div>

        <form wire:submit.prevent="sendMessage" class="feed-input-area">
            <div class="feed-input-wrapper">
                <ion-icon name="add-outline" style="font-size: 2.2rem; color: var(--text-mut); cursor: pointer;"></ion-icon>
                <input type="text" wire:model="newMessage" class="feed-input" placeholder="Type a message...">
                <button type="submit" class="feed-btn" @if(empty($newMessage)) disabled style="opacity: 0.3; cursor: default;" @endif>
                    <ion-icon name="paper-plane"></ion-icon>
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
