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
                <span style="position: absolute; top: 0px; right: 0px; background: var(--brand-primary); width: 10px; height: 10px; border-radius: 50%; border: 1.5px solid #fff;"></span>
            @endif
        </div>
        HTML;
    }
}
