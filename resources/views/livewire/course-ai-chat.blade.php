<div>
    <!-- Offcanvas Overlay -->
    @if($isOpen)
        <div class="chat-overlay" wire:click="toggleChat" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); z-index: 100;"></div>
    @endif

    <!-- Offcanvas Panel -->
    <div class="ai-chat-panel {{ $isOpen ? 'open' : '' }}" style="position: fixed; top: 0; right: -400px; width: 400px; height: 100vh; background: #fff; z-index: 101; box-shadow: -4px 0 15px rgba(0,0,0,0.1); transition: right 0.3s ease; display: flex; flex-direction: column;">
        
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="{{ asset('fitlife-assets/images/ai-icon.png') }}" style="width: 36px; height: 36px; border-radius: 8px;" alt="AI Tutor">
                <div>
                    <h3 style="font-size: 1.4rem; font-weight: 700; color: #1c1c1c; margin: 0; line-height: 1.2;">Course AI Tutor</h3>
                    
                </div>
            </div>
            <button wire:click="toggleChat" style="background: none; border: none; font-size: 2.5rem; cursor: pointer; color: #666; line-height: 1;">&times;</button>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; background: #fdfdfd;" id="chatMessagesBox">
            @foreach($messages as $msg)
                <div style="display: flex; gap: 10px; flex-direction: {{ $msg['role'] === 'user' ? 'row-reverse' : 'row' }};">
                    <!-- Avatar -->
                    @if($msg['role'] === 'assistant')
                        <img src="{{ asset('fitlife-assets/images/ai-icon.png') }}" style="width: 32px; height: 32px; border-radius: 6px; flex-shrink: 0;" alt="AI">
                    @else
                        @if(auth()->check() && auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; object-fit: cover;" alt="User">
                        @else
                            <div style="width: 32px; height: 32px; background: var(--brand-primary); border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.2rem; font-weight: bold;">
                                {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'U' }}
                            </div>
                        @endif
                    @endif

                    <!-- Bubble -->
                    <div class="chat-bubble {{ $msg['role'] }}" style="max-width: 75%; padding: 12px 16px; border-radius: 12px; font-size: 1.3rem; line-height: 1.5; {{ $msg['role'] === 'user' ? 'background: var(--brand-primary); color: #fff; border-bottom-right-radius: 2px;' : 'background: #f0f2f5; color: #1c1c1c; border-bottom-left-radius: 2px; overflow-wrap: anywhere;' }}">
                        {!! Str::markdown($msg['content']) !!}
                    </div>
                </div>
            @endforeach
            
            <div wire:loading.flex wire:target="sendMessage" style="gap: 10px;">
                <img src="{{ asset('fitlife-assets/images/ai-icon.png') }}" style="width: 32px; height: 32px; border-radius: 6px; flex-shrink: 0;" alt="AI">
                <div style="padding: 12px 16px; border-radius: 12px; background: #f0f2f5; color: #888; font-size: 1.2rem; font-style: italic;">
                    Thinking...
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div style="padding: 16px 20px; border-top: 1px solid #eee; background: #fff;">
            <form wire:submit.prevent="sendMessage" style="display: flex; gap: 10px;">
                <input type="text" wire:model="newMessage" placeholder="Ask about this course..." style="flex: 1; padding: 12px 16px; border: 1px solid #ddd; border-radius: 20px; font-size: 1.3rem; outline: none;" required>
                <button type="submit" style="background: var(--brand-primary); color: #fff; border: none; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.8rem;" wire:loading.attr="disabled">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </form>
        </div>
    </div>

    <style>
        .ai-chat-panel.open { right: 0 !important; }
        @media (max-width: 500px) { .ai-chat-panel { width: 100% !important; right: -100% !important; } }
        
        .chat-bubble p { margin-bottom: 8px; }
        .chat-bubble p:last-child { margin-bottom: 0; }
        .chat-bubble a { color: inherit; text-decoration: underline; font-weight: 600; transition: opacity 0.2s; }
        .chat-bubble a:hover { opacity: 0.8; }
        .chat-bubble ul, .chat-bubble ol { margin-bottom: 8px; padding-left: 20px; }
        .chat-bubble ul:last-child, .chat-bubble ol:last-child { margin-bottom: 0; }
        .chat-bubble strong { font-weight: 700; }
    </style>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('chat-updated', () => {
                // Use a short timeout to let the DOM morph finish first
                setTimeout(() => {
                    const chatBox = document.getElementById('chatMessagesBox');
                    if (chatBox) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                        
                        // Force all links in the chat to open in a new tab
                        const links = chatBox.querySelectorAll('.chat-bubble a');
                        links.forEach(link => {
                            link.setAttribute('target', '_blank');
                            link.setAttribute('rel', 'noopener noreferrer');
                        });
                    }
                }, 50);
            });
        });
    </script>
</div>
