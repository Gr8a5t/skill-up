@extends('layouts.dashboard')

@section('title', 'Chats')

@push('styles')
    <style>
        /* Override content-area for full bleed chat split */
        .content-area { padding: 0 !important; height: calc(100vh - 80px); overflow: hidden; background: #fff; }
        
        .chat-layout { display: flex; height: 100%; background: #fff; }
        
        /* Left Column: Conversation List */
        .chat-side { width: 360px; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; flex-shrink: 0; background: #fff; z-index: 10; }
        .list-header { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); background: #fff; }
        .chat-title { font-size: 2rem; font-weight: 500; color: #000; }
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .mark-read { color: var(--brand-primary); font-size: 1.3rem; font-weight: 500; text-decoration: none; }
        .filter-dropdown { display: flex; align-items: center; gap: 6px; color: var(--text-mut); font-size: 1.3rem; cursor: pointer; font-weight: 500; }

        .conversations-list { flex-grow: 1; overflow-y: auto; }
        .chat-item { display: flex; padding: 16px 20px; gap: 14px; border-bottom: 1px solid #f2f2f2; text-decoration: none; color: inherit; transition: background 0.2s; align-items: center; }
        .chat-item:hover { background: #fafafa; }
        .chat-item.active { background: rgba(255, 69, 0, 0.03); }
        
        .chat-avatar { width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0; background: #f0f2f5; overflow: hidden; border: 1px solid #eee; }
        .chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
        
        .chat-content { flex-grow: 1; min-width: 0; }
        .chat-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .chat-name { font-size: 1.5rem; color: #050505; font-weight: 400; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-item.unread .chat-name { font-weight: 700; }
        .chat-time { font-size: 1.2rem; color: var(--text-mut); }
        .chat-preview { font-size: 1.4rem; color: var(--text-mut); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .unread-dot { width: 12px; height: 12px; border-radius: 50%; background: var(--brand-primary); flex-shrink: 0; margin-left: 10px; }

        /* Right Column: Message Feed */
        .chat-main { flex-grow: 1; display: flex; flex-direction: column; background: #f9f9f9; min-width: 0; }
        .feed-header { height: 60px; padding: 0 24px; background: #fff; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .feed-user { display: flex; align-items: center; gap: 12px; }
        .feed-user-name { font-size: 1.5rem; font-weight: 600; color: #000; }
        .feed-status { font-size: 1.1rem; color: #23a55a; font-weight: 500; display: flex; align-items: center; gap: 4px; }
        .status-dot { width: 8px; height: 8px; background: #23a55a; border-radius: 50%; }
        
        .feed-content { flex-grow: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 16px; }
        .msg-line { display: flex; flex-direction: column; max-width: 75%; }
        .msg-line.me { align-self: flex-end; align-items: flex-end; }
        .msg-line.other { align-self: flex-start; align-items: flex-start; }
        
        .msg-bubble { padding: 10px 16px; border-radius: 18px; font-size: 1.4rem; line-height: 1.5; }
        .msg-line.me .msg-bubble { background: var(--brand-primary); color: #fff; border-bottom-right-radius: 4px; }
        .msg-line.other .msg-bubble { background: #fff; color: #000; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .msg-time { font-size: 1.1rem; color: var(--text-mut); margin-top: 4px; font-weight: 500; }

        .feed-input-area { padding: 20px 24px; background: #fff; border-top: 1px solid var(--border-color); flex-shrink: 0; }
        .feed-input-wrapper { display: flex; align-items: center; gap: 12px; background: #f0f2f5; padding: 10px 18px; border-radius: 24px; }
        .feed-input { flex-grow: 1; border: none; background: transparent; font-family: inherit; font-size: 1.4rem; color: #000; }
        .feed-input:focus { outline: none; }
        .feed-btn { background: none; border: none; font-size: 2.2rem; cursor: pointer; color: var(--brand-primary); display: flex; align-items: center; justify-content: center; transition: 0.2s; }

        @media (max-width: 992px) {
            .content-area { height: calc(100vh - 80px - 70px); } /* Adjust for mobile nav (70px) */
            body { padding-bottom: 0 !important; }
        }
    </style>
@endpush

@section('content')
    <livewire:chat-component :recipientId="request('user_id')" />
@endsection
