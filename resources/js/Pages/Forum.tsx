import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

interface User {
    id: number;
    name: string;
    avatar?: string;
}

interface Post {
    id: number;
    user: User;
    body: string;
    tags: string[];
    created_at: string;
    likes_count: number;
    comments_count: number;
}

interface ForumProps {
    posts: Post[];
}

export default function Forum({ posts }: ForumProps) {
    const topics = ['For you', 'Topics', 'Web Designer', 'UI Designer', '#frontenddevelopment'];
    const [activeTab, setActiveTab] = useState('For you');

    const { data, setData, post, reset, processing, errors } = useForm({
        body: '',
        tab: 'For you',
    });

    const submitPost = (e: React.FormEvent) => {
        e.preventDefault();
        post('/forum/post', {
            onSuccess: () => reset('body'),
        });
    };

    const handleTabChange = (tab: string) => {
        setActiveTab(tab);
        setData('tab', tab);
    };

    // Filter posts locally based on selected tab tag
    const filteredPosts = posts.filter(p => {
        if (activeTab === 'For you' || activeTab === 'Topics') return true;
        const normalizedTab = activeTab.toLowerCase().replace('#', '');
        return p.tags.some(tag => tag.toLowerCase().replace(' ', '') === normalizedTab);
    });

    const formatDiffForHumans = (dateString: string) => {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now.getTime() - date.getTime();
        const diffMins = Math.floor(diffMs / 60000);
        if (diffMins < 1) return 'just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        const diffHrs = Math.floor(diffMins / 60);
        if (diffHrs < 24) return `${diffHrs}h ago`;
        const diffDays = Math.floor(diffHrs / 24);
        return `${diffDays}d ago`;
    };

    return (
        <DashboardLayout title="Forum">
            <style dangerouslySetInnerHTML={{__html: `
                .forum-container { display: flex; gap: 30px; margin-top: 20px; padding: 20px; }
                .forum-main { flex: 1; min-width: 0; }
                .forum-filters { display: flex; gap: 12px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
                .forum-filters::-webkit-scrollbar { display: none; }
                
                .filter-pill { padding: 10px 20px; border-radius: 20px; font-weight: 600; font-size: 1.3rem; border: none; cursor: pointer; white-space: nowrap; transition: 0.2s; }
                
                .create-post-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 30px; }
                
                .forum-sidebar { width: 320px; flex-shrink: 0; display: flex; flex-direction: column; gap: 24px; }
                .post-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 20px; }
                
                @media (max-width: 992px) {
                    .forum-container { flex-direction: column; }
                    .forum-sidebar { width: 100%; }
                }
            `}} />

            <div className="forum-container">
                {/* Main Feed */}
                <div className="forum-main">
                    {/* Filters */}
                    <div className="forum-filters">
                        {topics.map(topic => (
                            <button 
                                key={topic}
                                onClick={() => handleTabChange(topic)}
                                className="filter-pill"
                                style={activeTab === topic ? {
                                    background: 'var(--brand-primary)',
                                    color: '#fff'
                                } : {
                                    background: 'var(--bg-surface)',
                                    color: 'var(--text-mut)',
                                    border: '1px solid var(--border-color)'
                                }}
                            >
                                {topic}
                            </button>
                        ))}
                    </div>

                    {/* Create Post */}
                    <div className="create-post-card">
                        <div style={{ display: 'flex', gap: '16px', alignItems: 'flex-start' }}>
                            <div className="user-avatar" style={{ width: '48px', height: '48px', borderRadius: '50%', background: '#f0ebff', color: '#8e54e9', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 'bold', flexShrink: 0, overflow: 'hidden', fontSize: '1.4rem' }}>
                                U
                            </div>
                            <div style={{ flex: 1 }}>
                                <form onSubmit={submitPost}>
                                    <textarea 
                                        value={data.body}
                                        onChange={e => setData('body', e.target.value)}
                                        placeholder={`What are you working on?`} 
                                        style={{ width: '100%', border: 'none', background: 'transparent', fontSize: '1.6rem', color: 'var(--text-main)', outline: 'none', resize: 'none', minHeight: '60px', marginTop: '8px', fontWeight: 500 }}
                                    />
                                    {errors.body && <span style={{ color: 'red', fontSize: '1.2rem' }}>{errors.body}</span>}
                                    
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '16px', paddingTop: '16px', borderTop: '1px solid var(--border-color)' }}>
                                        <div style={{ display: 'flex', gap: '16px', color: 'var(--text-mut)', fontSize: '1.6rem' }}>
                                            <button type="button" style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'inherit', display: 'flex', alignItems: 'center' }}>
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            </button>
                                            <button type="button" style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'inherit', display: 'flex', alignItems: 'center' }}>
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                            </button>
                                            <button type="button" style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'inherit', display: 'flex', alignItems: 'center' }}>
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                                            </button>
                                            <button type="button" style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'inherit', fontSize: '1.2rem', fontWeight: 600, display: 'flex', alignItems: 'center', gap: '6px' }}>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg> Add tags
                                            </button>
                                        </div>
                                        <button 
                                            type="submit" 
                                            disabled={processing}
                                            style={{ background: '#1c1c1c', color: '#fff', padding: '10px 24px', borderRadius: '24px', fontSize: '1.2rem', fontWeight: 'bold', border: 'none', cursor: 'pointer' }}
                                        >
                                            {processing ? 'Posting...' : 'Post'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {/* Suggested Topics */}
                    <div style={{ marginBottom: '30px' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                            <h3 style={{ fontSize: '1.2rem', color: 'var(--text-mut)', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '1px' }}>Suggested Topics</h3>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--text-mut)', cursor: 'pointer' }}><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div style={{ display: 'flex', gap: '20px', overflowX: 'auto', paddingBottom: '10px', scrollbarWidth: 'none' }}>
                            {/* Card 1 */}
                            <div style={{ minWidth: '320px', height: '180px', borderRadius: '16px', background: '#0f4c3a', color: '#fff', padding: '20px', position: 'relative', overflow: 'hidden', display: 'flex', flexDirection: 'column', justifyContent: 'space-between' }}>
                                <div style={{ position: 'absolute', right: '-20px', bottom: '-20px', width: '150px', height: '150px', background: 'rgba(255,255,255,0.1)', borderRadius: '50%' }}></div>
                                <div style={{ position: 'absolute', left: '40px', top: '-30px', width: '100px', height: '100px', background: 'rgba(255,255,255,0.05)', borderRadius: '50%' }}></div>
                                
                                <div style={{ position: 'relative', zIndex: 1 }}>
                                    <h4 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 2.001c-3.313 0-6 2.686-6 6v3.013h-4v-3.013h4v3.013h4v-3.013c0-3.314 2.687-6 6-6zM8 11.014c-3.313 0-6 2.687-6 6s2.687 6 6 6 6-2.687 6-6v-3.013h-4v-2.987h-2z"/></svg> 
                                        # Config Makeathon 
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ marginLeft: 'auto' }}><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </h4>
                                    <p style={{ fontSize: '1.2rem', opacity: 0.9 }}>$100k in prizes for ideas built in Figma</p>
                                </div>
                                <div style={{ position: 'relative', zIndex: 1, display: 'flex', gap: '16px' }}>
                                    <div>
                                        <div style={{ fontSize: '1.4rem', fontWeight: 700 }}>$100K</div>
                                        <div style={{ fontSize: '1.1rem', opacity: 0.8 }}>Prize</div>
                                    </div>
                                    <div>
                                        <div style={{ fontSize: '1.4rem', fontWeight: 700 }}>10d</div>
                                        <div style={{ fontSize: '1.1rem', opacity: 0.8 }}>Left</div>
                                    </div>
                                </div>
                            </div>

                            {/* Card 2 */}
                            <div style={{ minWidth: '320px', height: '180px', borderRadius: '16px', background: '#2a2a2a', color: '#fff', padding: '20px', position: 'relative', overflow: 'hidden', display: 'flex', flexDirection: 'column', justifyContent: 'space-between' }}>
                                <div style={{ position: 'relative', zIndex: 1 }}>
                                    <h4 style={{ fontSize: '1.4rem', fontWeight: 700, display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                                        <span style={{ fontSize: '1rem', background: '#fff', color: '#000', padding: '2px 6px', borderRadius: '4px' }}>Ta</span> # CapCut DesignStudio 
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ marginLeft: 'auto' }}><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </h4>
                                    <p style={{ fontSize: '1.2rem', opacity: 0.9 }}>Submit your design work to join the challenge.</p>
                                </div>
                                <div style={{ position: 'relative', zIndex: 1, display: 'flex', gap: '16px', alignItems: 'flex-end' }}>
                                    <div>
                                        <div style={{ fontSize: '1.4rem', fontWeight: 700 }}>$7.5K</div>
                                        <div style={{ fontSize: '1.1rem', opacity: 0.8 }}>Prize</div>
                                    </div>
                                    <div>
                                        <div style={{ fontSize: '1.4rem', fontWeight: 700 }}>Ended</div>
                                        <div style={{ fontSize: '1.1rem', opacity: 0.8 }}>Status</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Feed */}
                    <div className="forum-feed" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                        {filteredPosts.length > 0 ? (
                            filteredPosts.map(post => (
                                <div key={post.id} className="post-card">
                                    <div className="post-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '16px' }}>
                                        <div style={{ display: 'flex', gap: '14px', alignItems: 'center' }}>
                                            <div className="user-avatar" style={{ width: '48px', height: '48px', borderRadius: '50%', background: '#ffe4ef', color: '#ff4aa0', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '1.4rem', fontWeight: 'bold', overflow: 'hidden' }}>
                                                {post.user.avatar ? (
                                                    <img src={post.user.avatar} style={{ width: '100%', height: '100%', objectFit: 'cover' }} alt="" />
                                                ) : (
                                                    post.user.name.substring(0, 1)
                                                )}
                                            </div>
                                            <div style={{ textAlign: 'left' }}>
                                                <h4 style={{ fontSize: '1.5rem', fontWeight: 700, marginBottom: '2px' }}>{post.user.name}</h4>
                                                <span style={{ fontSize: '1.2rem', color: 'var(--text-mut)' }}>{formatDiffForHumans(post.created_at)}</span>
                                            </div>
                                        </div>
                                        <button style={{ background: 'none', border: 'none', color: 'var(--text-mut)', fontWeight: 600, fontSize: '1.3rem', cursor: 'pointer' }}>Follow</button>
                                    </div>
                                    
                                    <div className="post-body" style={{ fontSize: '1.5rem', lineHeight: 1.6, color: 'var(--text-main)', marginBottom: '16px', textAlign: 'left' }}>
                                        {post.body}
                                    </div>

                                    {post.tags && post.tags.length > 0 && (
                                        <div style={{ display: 'flex', gap: '8px', marginBottom: '16px' }}>
                                            {post.tags.map((tag, idx) => (
                                                <span key={idx} style={{ background: '#f0f0f0', padding: '6px 12px', borderRadius: '12px', fontSize: '1.1rem', color: 'var(--text-mut)', fontWeight: 600 }}>
                                                    #{tag.toLowerCase().replace(' ', '')}
                                                </span>
                                            ))}
                                        </div>
                                    )}

                                    <div className="post-actions" style={{ display: 'flex', gap: '24px', color: 'var(--text-mut)', fontSize: '1.4rem', borderTop: '1px solid var(--border-color)', paddingTop: '16px' }}>
                                        <button style={{ background: 'none', border: 'none', color: 'inherit', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px' }}>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg> 
                                            {post.likes_count}
                                        </button>
                                        <button style={{ background: 'none', border: 'none', color: 'inherit', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px' }}>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> 
                                            {post.comments_count}
                                        </button>
                                        <button style={{ background: 'none', border: 'none', color: 'inherit', cursor: 'pointer', marginLeft: 'auto' }}>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div style={{ textAlign: 'center', padding: '40px', color: 'var(--text-mut)', fontSize: '1.4rem' }}>
                                No posts yet. Be the first to share!
                            </div>
                        )}
                    </div>
                </div>

                {/* Right Sidebar */}
                <div className="forum-sidebar">
                    {/* Challenges */}
                    <div style={{ background: 'var(--bg-surface)', border: '1px solid var(--border-color)', borderRadius: '16px', padding: '20px' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                            <h3 style={{ fontSize: '1.2rem', color: 'var(--text-mut)', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '1px' }}>Challenges</h3>
                            <a href="#" style={{ fontSize: '1.3rem', color: 'var(--brand-primary)', textDecoration: 'none', fontWeight: 600 }}>View all</a>
                        </div>
                        <div style={{ display: 'flex', gap: '14px', alignItems: 'center' }}>
                            <div style={{ width: '48px', height: '48px', borderRadius: '10px', background: '#1c1c1c', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '2rem' }}>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path></svg>
                            </div>
                            <div style={{ textAlign: 'left' }}>
                                <h4 style={{ fontSize: '1.4rem', fontWeight: 700, marginBottom: '2px' }}>Config Makeathon</h4>
                                <p style={{ fontSize: '1.2rem', color: 'var(--text-mut)' }}>$100K • 10d left</p>
                            </div>
                        </div>
                    </div>

                    {/* Trending Topics */}
                    <div style={{ background: 'var(--bg-surface)', border: '1px solid var(--border-color)', borderRadius: '16px', padding: '20px' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                            <h3 style={{ fontSize: '1.2rem', color: 'var(--text-mut)', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '1px' }}>Trending</h3>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--text-mut)' }}><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                        </div>
                        
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '20px', textAlign: 'left' }}>
                            <div>
                                <h4 style={{ fontSize: '1.4rem', fontWeight: 700, marginBottom: '6px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--text-mut)' }}><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg> 
                                    Claude
                                </h4>
                                <p style={{ fontSize: '1.3rem', color: 'var(--text-mut)', lineHeight: 1.5 }}>Claude has entered the design space. How are you using Claude Design?</p>
                            </div>
                            <div>
                                <h4 style={{ fontSize: '1.4rem', fontWeight: 700, marginBottom: '6px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--text-mut)' }}><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg> 
                                    SkillUp University
                                </h4>
                                <p style={{ fontSize: '1.3rem', color: 'var(--text-mut)', lineHeight: 1.5 }}>Learn from expert creatives how to earn more using next-gen AI tools.</p>
                            </div>
                            <div>
                                <h4 style={{ fontSize: '1.4rem', fontWeight: 700, marginBottom: '6px' }}>#creativeaiflow</h4>
                                <p style={{ fontSize: '1.3rem', color: 'var(--text-mut)', lineHeight: 1.5 }}>Creative AI workflows are evolving. What tools do you use, and what are their strengths?</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
