import React, { useState, useEffect, useRef } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

interface Conversation {
    id: string; // Hashed recipient ID
    name: string;
    avatar: string;
    message: string;
    time: string;
    unread: boolean;
    unread_count: number;
    is_online: boolean;
}

interface Message {
    id: number;
    sender_id: number;
    recipient_id: number;
    message: string;
    created_at: string;
}

interface ActiveRecipient {
    id: number;
    name: string;
    avatar?: string;
    is_online: boolean;
}

interface ChatsProps {
    conversations: Conversation[];
    messages: Message[];
    activeRecipient: ActiveRecipient | null;
    auth: {
        user: {
            id: number;
            name: string;
        };
    };
}

export default function Chats({ conversations, messages, activeRecipient, auth }: ChatsProps) {
    const [searchTerm, setSearchTerm] = useState('');
    const [editingMessageId, setEditingMessageId] = useState<number | null>(null);
    const [editingText, setEditingText] = useState('');

    const messagesEndRef = useRef<HTMLDivElement>(null);

    const { data, setData, post, reset, processing } = useForm({
        recipient_id: activeRecipient ? activeRecipient.id : '',
        message: '',
    });

    // Auto-scroll messages feed to bottom
    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    // Poll messages silently every 4 seconds
    useEffect(() => {
        const interval = setInterval(() => {
            router.reload({ 
                only: ['messages', 'conversations']
            });
        }, 4000);
        return () => clearInterval(interval);
    }, []);

    // Filter conversations local search
    const filteredConversations = conversations.filter(c => 
        c.name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const handleSelectRecipient = (recipientIdHash: string) => {
        router.visit(`/chats?user_id=${recipientIdHash}`, { 
            preserveScroll: true, 
            preserveState: true 
        });
    };

    const handleSendMessage = (e: React.FormEvent) => {
        e.preventDefault();
        if (!data.message.trim() || !activeRecipient) return;

        post('/chats/send', {
            preserveScroll: true,
            onSuccess: () => {
                reset('message');
                scrollToBottom();
            }
        });
    };

    // Edit message action
    const startEditing = (msg: Message) => {
        // Only allow if within 5m
        const diffMins = (new Date().getTime() - new Date(msg.created_at).getTime()) / 60000;
        if (diffMins >= 5) {
            alert('Time limit for editing (5m) has passed.');
            return;
        }
        setEditingMessageId(msg.id);
        setEditingText(msg.message);
    };

    const handleSaveEdit = (msgId: number) => {
        if (!editingText.trim()) return;

        router.post('/chats/edit', {
            message_id: msgId,
            message: editingText
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingMessageId(null);
                setEditingText('');
            }
        });
    };

    // Delete message action
    const handleDeleteMessage = (msgId: number) => {
        if (confirm('Delete this message?')) {
            router.delete(`/chats/delete/${msgId}`, {
                preserveScroll: true
            });
        }
    };

    return (
        <DashboardLayout title="Chats">
            <style dangerouslySetInnerHTML={{__html: `
                body { background: #fff !important; overflow: hidden !important; }
                .chat-layout { display: flex; height: calc(100vh - 80px); background: #fff; overflow: hidden; }
                
                .chat-side { width: 360px; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; flex-shrink: 0; background: #fff; z-index: 10; }
                .list-header { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); background: #fff; }
                .chat-title { font-size: 2rem; font-weight: 500; color: #000; }
                
                .conversations-list { flex-grow: 1; overflow-y: auto; }
                .chat-item { display: flex; padding: 16px 20px; gap: 14px; border-bottom: 1px solid #f2f2f2; text-decoration: none; color: inherit; transition: background 0.2s; align-items: center; cursor: pointer; }
                .chat-item:hover { background: #fafafa; }
                .chat-item.active { background: rgba(255, 69, 0, 0.03); }
                
                .chat-avatar { width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0; background: #f0f2f5; overflow: hidden; border: 1px solid #eee; position: relative; }
                .chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
                
                .chat-content { flex-grow: 1; min-width: 0; text-align: left; }
                .chat-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; justify-content: space-between; }
                .chat-name { font-size: 1.5rem; color: #050505; font-weight: 400; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .chat-item.unread .chat-name { font-weight: 700; }
                .chat-time { font-size: 1.2rem; color: var(--text-mut); }
                .chat-preview { font-size: 1.4rem; color: var(--text-mut); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .unread-dot { width: 12px; height: 12px; border-radius: 50%; background: var(--brand-primary); flex-shrink: 0; margin-left: 10px; }

                .chat-main { flex-grow: 1; display: flex; flex-direction: column; background: #f9f9f9; min-width: 0; }
                .feed-header { height: 60px; padding: 0 24px; background: #fff; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
                .feed-user { display: flex; align-items: center; gap: 12px; }
                .feed-user-name { font-size: 1.5rem; font-weight: 600; color: #000; }
                .feed-status { font-size: 1.1rem; color: #23a55a; font-weight: 500; display: flex; align-items: center; gap: 4px; }
                .status-dot { width: 8px; height: 8px; background: #23a55a; border-radius: 50%; }
                
                .feed-content { flex-grow: 1; overflow-y: auto; overflow-x: hidden; padding: 24px; display: flex; flex-direction: column; gap: 16px; }
                .msg-line { display: flex; flex-direction: column; max-width: 75%; position: relative; group: hover; }
                .msg-line.me { align-self: flex-end; align-items: flex-end; }
                .msg-line.other { align-self: flex-start; align-items: flex-start; }
                
                .msg-bubble { padding: 10px 16px; border-radius: 18px; font-size: 1.4rem; line-height: 1.5; position: relative; }
                .msg-line.me .msg-bubble { background: var(--brand-primary); color: #fff; border-bottom-right-radius: 4px; }
                .msg-line.other .msg-bubble { background: #fff; color: #000; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
                .msg-time { font-size: 1.1rem; color: var(--text-mut); margin-top: 4px; font-weight: 500; }

                .feed-input-area { padding: 20px 24px; background: #fff; border-top: 1px solid var(--border-color); flex-shrink: 0; }
                .feed-input-wrapper { display: flex; align-items: center; gap: 12px; background: #f0f2f5; padding: 10px 18px; border-radius: 24px; }
                .feed-input { flex-grow: 1; border: none; background: transparent; font-family: inherit; font-size: 1.4rem; color: #000; }
                .feed-input:focus { outline: none; }
                .feed-btn { background: none; border: none; font-size: 2.2rem; cursor: pointer; color: var(--brand-primary); display: flex; align-items: center; justify-content: center; transition: 0.2s; }

                .msg-actions-hover { display: none; gap: 8px; position: absolute; top: -20px; right: 0; background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 2px 6px; z-index: 100; font-size: 1rem; }
                .msg-line.me:hover .msg-actions-hover { display: flex; }
                .msg-action-btn { background: none; border: none; cursor: pointer; color: var(--text-mut); }
                .msg-action-btn:hover { color: var(--brand-primary); }

                @media (max-width: 992px) {
                    .chat-layout { height: calc(100vh - 80px); } 
                    body { padding-bottom: 0 !important; }
                    .feed-input-area { padding-bottom: calc(20px + 75px) !important; }
                }
            `}} />

            <div className="chat-layout">
                {/* Conversation List Sidebar */}
                <div className="chat-side">
                    <div className="list-header">
                        <span className="chat-title">Chats</span>
                        <div style={{ position: 'relative', flex: 1, marginLeft: '12px' }}>
                            <input 
                                type="text" 
                                placeholder="Search contacts..." 
                                value={searchTerm}
                                onChange={e => setSearchTerm(e.target.value)}
                                style={{ width: '100%', padding: '6px 12px', border: '1px solid var(--border-color)', borderRadius: '16px', fontSize: '1.2rem' }}
                            />
                        </div>
                    </div>

                    <div className="conversations-list">
                        {filteredConversations.map(conv => (
                            <div 
                                key={conv.id}
                                onClick={() => handleSelectRecipient(conv.id)}
                                className={`chat-item ${activeRecipient && activeRecipient.id.toString() === conv.id ? 'active' : ''} ${conv.unread ? 'unread' : ''}`}
                            >
                                <div className="chat-avatar">
                                    <img src={conv.avatar} alt={conv.name} />
                                    {conv.is_online && (
                                        <span style={{ position: 'absolute', bottom: '2px', right: '2px', width: '10px', height: '10px', background: '#23a55a', borderRadius: '50%', border: '2px solid #fff' }} />
                                    )}
                                </div>
                                <div className="chat-content">
                                    <div className="chat-meta">
                                        <span className="chat-name">{conv.name}</span>
                                        <span className="chat-time">{conv.time}</span>
                                    </div>
                                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                                        <p className="chat-preview">{conv.message}</p>
                                        {conv.unread && <span className="unread-dot" />}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Message Feed Main area */}
                <div className="chat-main">
                    {activeRecipient ? (
                        <>
                            <div className="feed-header">
                                <div className="feed-user">
                                    <span className="feed-user-name">{activeRecipient.name}</span>
                                    {activeRecipient.is_online && (
                                        <span className="feed-status">
                                            <span className="status-dot" /> Online
                                        </span>
                                    )}
                                </div>
                            </div>

                            <div className="feed-content">
                                {messages.map(msg => {
                                    const isMe = msg.sender_id === auth.user.id;
                                    const isEditing = editingMessageId === msg.id;

                                    return (
                                        <div key={msg.id} className={`msg-line ${isMe ? 'me' : 'other'}`}>
                                            {isMe && !isEditing && (
                                                <div className="msg-actions-hover">
                                                    <button onClick={() => startEditing(msg)} className="msg-action-btn">Edit</button>
                                                    <button onClick={() => handleDeleteMessage(msg.id)} className="msg-action-btn">Delete</button>
                                                </div>
                                            )}

                                            <div className="msg-bubble">
                                                {isEditing ? (
                                                    <div style={{ display: 'flex', gap: '8px' }}>
                                                        <input 
                                                            type="text" 
                                                            value={editingText} 
                                                            onChange={e => setEditingText(e.target.value)}
                                                            style={{ padding: '4px 8px', border: '1px solid #ccc', borderRadius: '4px', color: '#000' }}
                                                        />
                                                        <button onClick={() => handleSaveEdit(msg.id)} style={{ background: 'var(--brand-primary)', border: 'none', color: '#fff', padding: '2px 8px', borderRadius: '4px', cursor: 'pointer' }}>Save</button>
                                                        <button onClick={() => setEditingMessageId(null)} style={{ background: '#ddd', border: 'none', color: '#000', padding: '2px 8px', borderRadius: '4px', cursor: 'pointer' }}>Cancel</button>
                                                    </div>
                                                ) : (
                                                    msg.message
                                                )}
                                            </div>
                                            <span className="msg-time">
                                                {new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                            </span>
                                        </div>
                                    );
                                })}
                                <div ref={messagesEndRef} />
                            </div>

                            <div className="feed-input-area">
                                <form onSubmit={handleSendMessage} className="feed-input-wrapper">
                                    <input 
                                        type="text" 
                                        className="feed-input" 
                                        placeholder="Type your message..." 
                                        value={data.message}
                                        onChange={e => {
                                            setData(data => ({
                                                ...data,
                                                message: e.target.value,
                                                recipient_id: activeRecipient.id
                                            }));
                                        }}
                                    />
                                    <button type="submit" className="feed-btn" disabled={processing}>
                                        <ion-icon name="send"></ion-icon>
                                    </button>
                                </form>
                            </div>
                        </>
                    ) : (
                        <div style={{ display: 'flex', flexGrow: 1, alignItems: 'center', justifyContent: 'center', color: 'var(--text-mut)', fontSize: '1.6rem' }}>
                            Select a conversation to start messaging.
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
