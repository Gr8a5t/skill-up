<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ChatComponent extends Component
{
    public $activeRecipientHash;
    public $newMessage;
    public $search = '';
    public $editingMessageId = null;
    public $editingMessageText = '';

    protected $rules = [
        'newMessage' => 'required|string|max:1000',
    ];

    public function mount($recipientId = null)
    {
        if ($recipientId) {
            $this->activeRecipientHash = $recipientId;
        } else {
            // Default to the most recent conversation
            $latestMessage = ChatMessage::where('sender_id', auth()->id())
                ->orWhere('recipient_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestMessage) {
                $recipientId = $latestMessage->sender_id === auth()->id() 
                    ? $latestMessage->recipient_id 
                    : $latestMessage->sender_id;
                $this->activeRecipientHash = \App\Utils\HashId::encode($recipientId);
            }
        }
    }

    public function selectRecipient($id)
    {
        $this->activeRecipientHash = $id;
        $this->markAsRead();
        $this->reset('newMessage');
    }

    public function sendMessage()
    {
        $decoded = \App\Utils\HashId::decode($this->activeRecipientHash);
        if (empty($decoded)) return;

        $this->validate();

        ChatMessage::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $decoded[0],
            'message' => $this->newMessage,
        ]);

        $this->reset('newMessage');
        $this->dispatch('message-sent');
    }

    public function startEditMessage($id)
    {
        $message = ChatMessage::findOrFail($id);
        if ($message->sender_id !== auth()->id()) return;
        
        // Only allow editing if message is less than 5 minutes old
        if ($message->created_at->diffInMinutes() >= 5) {
            $this->dispatch('notify', ['message' => 'Time limit for editing (5m) has passed.', 'type' => 'error']);
            return;
        }

        $this->editingMessageId = $id;
        $this->editingMessageText = $message->message;
    }

    public function cancelEditMessage()
    {
        $this->editingMessageId = null;
        $this->editingMessageText = '';
    }

    public function updateMessage()
    {
        if (!$this->editingMessageId) return;

        $message = ChatMessage::findOrFail($this->editingMessageId);
        if ($message->sender_id !== auth()->id()) return;

        if ($message->created_at->diffInMinutes() >= 5) {
            $this->dispatch('notify', ['message' => 'Time limit for editing (5m) has passed.', 'type' => 'error']);
            $this->cancelEditMessage();
            return;
        }

        $this->validate([
            'editingMessageText' => 'required|string|max:1000',
        ]);

        $message->update([
            'message' => $this->editingMessageText,
        ]);

        $this->cancelEditMessage();
    }

    public function deleteMessage($id)
    {
        $message = ChatMessage::findOrFail($id);
        if ($message->sender_id !== auth()->id()) return;

        $message->delete();
        $this->dispatch('message-deleted');
    }

    public function markAsRead()
    {
        $decoded = \App\Utils\HashId::decode($this->activeRecipientHash);
        if (!empty($decoded)) {
            ChatMessage::where('sender_id', $decoded[0])
                ->where('recipient_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    public function getConversationsProperty()
    {
        $userId = auth()->id();

        // Fetch only the latest message ID per contact at the database level
        $latestMessages = ChatMessage::whereIn('id', function ($query) use ($userId) {
            $query->select(DB::raw('MAX(id)'))
                ->from('chat_messages')
                ->where('sender_id', $userId)
                ->orWhere('recipient_id', $userId)
                ->groupBy(DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN recipient_id ELSE sender_id END'));
        })
        ->select('id', DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN recipient_id ELSE sender_id END as contact_id'), 'created_at', 'message', 'is_read', 'sender_id')
        ->orderBy('created_at', 'desc')
        ->get();

        $conversations = [];
        foreach ($latestMessages as $msg) {
            $user = User::find($msg->contact_id);
            if (!$user) continue;

            $unreadCount = ChatMessage::where('sender_id', $user->id)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->count();

            $conversations[] = [
                'id' => \App\Utils\HashId::encode($user->id),
                'name' => $user->name,
                'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f0ebff&color=8e54e9',
                'message' => $msg->message,
                'time' => $msg->created_at->diffForHumans(null, true),
                'unread' => $unreadCount > 0,
                'unread_count' => $unreadCount,
                'is_online' => $user->isOnline(),
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
        $decoded = \App\Utils\HashId::decode($this->activeRecipientHash);
        if (empty($decoded)) return collect();
        $recipientId = $decoded[0];

        return ChatMessage::where(function ($q) use ($recipientId) {
                $q->where('sender_id', auth()->id())->where('recipient_id', $recipientId);
            })->orWhere(function ($q) use ($recipientId) {
                $q->where('sender_id', $recipientId)->where('recipient_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getActiveRecipientProperty()
    {
        $decoded = \App\Utils\HashId::decode($this->activeRecipientHash);
        return !empty($decoded) ? User::find($decoded[0]) : null;
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
