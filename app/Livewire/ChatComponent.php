<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ChatComponent extends Component
{
    public $activeRecipientId;
    public $newMessage;
    public $search = '';

    protected $rules = [
        'newMessage' => 'required|string|max:1000',
    ];

    public function mount($recipientId = null)
    {
        if ($recipientId) {
            $decoded = \App\Utils\HashId::decode($recipientId);
            $this->activeRecipientId = !empty($decoded) ? $decoded[0] : $recipientId;
        } else {
            // Default to the most recent conversation
            $latestMessage = ChatMessage::where('sender_id', auth()->id())
                ->orWhere('recipient_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestMessage) {
                $this->activeRecipientId = $latestMessage->sender_id === auth()->id() 
                    ? $latestMessage->recipient_id 
                    : $latestMessage->sender_id;
            }
        }
    }

    public function selectRecipient($id)
    {
        $this->activeRecipientId = $id;
        $this->markAsRead();
        $this->reset('newMessage');
    }

    public function sendMessage()
    {
        if (!$this->activeRecipientId) return;

        $this->validate();

        ChatMessage::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $this->activeRecipientId,
            'message' => $this->newMessage,
        ]);

        $this->reset('newMessage');
        $this->dispatch('message-sent');
    }

    public function markAsRead()
    {
        if ($this->activeRecipientId) {
            ChatMessage::where('sender_id', $this->activeRecipientId)
                ->where('recipient_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    public function getConversationsProperty()
    {
        $userId = auth()->id();

        // Subquery to get the latest message for each user interaction
        $latestMessages = ChatMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->select('id', DB::raw('CASE WHEN "sender_id" = ' . $userId . ' THEN "recipient_id" ELSE "sender_id" END as contact_id'), 'created_at', 'message', 'is_read', 'sender_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('contact_id');

        $conversations = [];
        foreach ($latestMessages as $msg) {
            $user = User::find($msg->contact_id);
            if (!$user) continue;

            $unreadCount = ChatMessage::where('sender_id', $user->id)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->count();

            $conversations[] = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f0ebff&color=8e54e9',
                'message' => $msg->message,
                'time' => $msg->created_at->diffForHumans(null, true),
                'unread' => $unreadCount > 0,
                'unread_count' => $unreadCount,
            ];
        }

        // Filter search
        if ($this->search) {
            $conversations = array_filter($conversations, function ($c) {
                return str_contains(strtolower($c['name']), strtolower($this->search));
            });
        }

        return $conversations;
    }

    public function getMessagesProperty()
    {
        if (!$this->activeRecipientId) return collect();

        return ChatMessage::where(function ($q) {
                $q->where('sender_id', auth()->id())->where('recipient_id', $this->activeRecipientId);
            })->orWhere(function ($q) {
                $q->where('sender_id', $this->activeRecipientId)->where('recipient_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getActiveRecipientProperty()
    {
        return User::find($this->activeRecipientId);
    }

    public function render()
    {
        return view('livewire.chat-component', [
            'conversations' => $this->conversations,
            'messages' => $this->messages,
            'activeRecipient' => $this->activeRecipient,
        ]);
    }
}
