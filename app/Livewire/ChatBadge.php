<?php

namespace App\Livewire;

use Livewire\Component;

class ChatBadge extends Component
{
    public function getUnreadCountProperty()
    {
        return auth()->check() ? auth()->user()->unreadMessagesCount() : 0;
    }

    public function render()
    {
        return <<<'HTML'
        <div wire:poll.10s>
            @if($this->unreadCount > 0)
                <span style="position: absolute; top: -5px; right: -5px; background: var(--brand-primary); color: #fff; font-size: 0.9rem; font-weight: 800; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;">
                    {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                </span>
            @endif
        </div>
        HTML;
    }
}
