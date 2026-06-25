import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

interface Lesson {
    video_id: string;
    title: string;
    time: string;
    progress: number;
    active: boolean;
}

interface Reply {
    id: number;
    user_id: number | null;
    user_name: string;
    avatar: string;
    content: string;
    created_at: string;
    likes: number;
    is_liked: boolean;
}

interface Comment {
    id: number;
    user_id: number | null;
    user_name: string;
    avatar: string;
    content: string;
    created_at: string;
    likes: number;
    is_liked: boolean;
    replies: Reply[];
}

interface Course {
    title: string;
    category: string;
    level: string;
    lessons_count: number;
    duration: string;
    video_id: string;
    recap: string;
    concepts: string[];
    source_files_url?: string;
    cheatsheet_url?: string;
}

interface CourseLearnProps {
    course: Course;
    lessons: Lesson[];
    slug: string;
    comments: Comment[];
    auth: {
        user?: {
            id: number;
            name: string;
            avatar?: string;
        };
    };
}

export default function CourseLearn({ course, lessons, slug, comments, auth }: CourseLearnProps) {
    const [activeTab, setActiveTab] = useState<'summary' | 'files' | 'resources' | 'comments'>('summary');
    const [aiChatOpen, setAiChatOpen] = useState(false);
    const [workspaceOpen, setWorkspaceOpen] = useState(false);
    const [monacoLoaded, setMonacoLoaded] = useState(false);

    // AI Chat Messages state
    const [aiMessages, setAiMessages] = useState<Array<{ role: 'user' | 'assistant'; content: string }>>([
        {
            role: 'assistant',
            content: `Hi! I'm your AI assistant for '${course.title}'. Ask me anything about the course materials!`
        }
    ]);
    const [aiInput, setAiInput] = useState('');
    const [aiSending, setAiSending] = useState(false);
    const aiChatEndRef = useRef<HTMLDivElement>(null);

    // Comments forms
    const newCommentForm = useForm({ content: '' });
    const replyForm = useForm({ content: '' });
    const [replyingCommentId, setReplyingCommentId] = useState<number | null>(null);

    // Edit Comment state
    const [editingCommentId, setEditingCommentId] = useState<number | null>(null);
    const editForm = useForm({ content: '' });

    // Workspace Resizing States
    const [videoWidth, setVideoWidth] = useState(45);
    const [consoleHeight, setConsoleHeight] = useState(30);
    const [isResizingV, setIsResizingV] = useState(false);
    const [isResizingH, setIsResizingH] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [consoleLogs, setConsoleLogs] = useState<string[]>([
        '~/skillup/project$ npm run dev',
        'VITE v5.0.0  ready in 250 ms',
        '',
        '  ➜  Local:   http://localhost:5173/',
        '  ➜  Network: use --host to expose'
    ]);

    // Monaco Editor Ref
    const editorRef = useRef<any>(null);

    // Auto scroll AI Chat
    useEffect(() => {
        aiChatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [aiMessages]);

    // Handle YouTube Video Loader and Progress Sync
    useEffect(() => {
        if (!window.YT) {
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode?.insertBefore(tag, firstScriptTag);
        }

        let ytPlayer: any;
        let progressInterval: any;

        const onPlayerStateChange = (event: any) => {
            if (event.data === window.YT.PlayerState.PLAYING) {
                progressInterval = setInterval(updateProgress, 5000);
            } else {
                if (progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;
                }
                if (event.data === window.YT.PlayerState.PAUSED || event.data === window.YT.PlayerState.ENDED) {
                    updateProgress();
                }
            }
        };

        const updateProgress = () => {
            if (!ytPlayer || !ytPlayer.getCurrentTime) return;
            const currentTime = ytPlayer.getCurrentTime();
            const duration = ytPlayer.getDuration();
            if (duration <= 0) return;

            fetch('/api/progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
                },
                body: JSON.stringify({
                    course_slug: slug,
                    video_id: course.video_id,
                    progress_seconds: currentTime,
                    total_seconds: duration,
                }),
            }).catch(console.error);
        };

        const initPlayer = () => {
            ytPlayer = new window.YT.Player('player', {
                videoId: course.video_id,
                playerVars: {
                    rel: 0,
                    playsinline: 1,
                },
                events: {
                    onStateChange: onPlayerStateChange,
                },
            });
        };

        if (window.YT && window.YT.Player) {
            initPlayer();
        } else {
            (window as any).onYouTubeIframeAPIReady = initPlayer;
        }

        return () => {
            if (progressInterval) clearInterval(progressInterval);
            if (ytPlayer && ytPlayer.destroy) ytPlayer.destroy();
        };
    }, [course.video_id, slug]);

    // Handle Workspace Monaco Initialization
    useEffect(() => {
        if (workspaceOpen) {
            const playerEl = document.getElementById('player');
            const workspaceVideoContainer = document.getElementById('workspace-video-container');
            if (playerEl && workspaceVideoContainer) {
                workspaceVideoContainer.appendChild(playerEl);
            }
            document.body.style.overflow = 'hidden';

            if (!monacoLoaded) {
                const loaderScript = document.createElement('script');
                loaderScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs/loader.min.js';
                loaderScript.onload = () => {
                    (window as any).require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs' }});
                    (window as any).require(['vs/editor/editor.main'], () => {
                        const container = document.getElementById('monaco-editor-container');
                        if (!container) return;

                        const defaultCode = [
                            '<html>',
                            '    <head>',
                            '        <link rel="stylesheet" href="style.css">',
                            '    </head>',
                            '    <body>',
                            '        <h1>People entered:</h1>',
                            '        <h2 id="count-el">0</h2>',
                            '        <script>',
                            '            document.getElementById("count-el").innerText = 5;',
                            '        </script>',
                            '    </body>',
                            '</html>'
                        ].join('\n');

                        editorRef.current = (window as any).monaco.editor.create(container, {
                            value: defaultCode,
                            language: 'html',
                            theme: 'vs-dark',
                            automaticLayout: true,
                            minimap: { enabled: false },
                            fontSize: 14,
                            fontFamily: "'Fira Code', 'Courier New', monospace"
                        });
                        setMonacoLoaded(true);
                    });
                };
                document.head.appendChild(loaderScript);
            } else if (editorRef.current) {
                setTimeout(() => editorRef.current.layout(), 100);
            }
        } else {
            const playerEl = document.getElementById('player');
            const originalVideoContainer = document.querySelector('.video-box');
            if (playerEl && originalVideoContainer) {
                originalVideoContainer.appendChild(playerEl);
            }
            document.body.style.overflow = '';
        }
    }, [workspaceOpen]);

    // Handle Workspace Resize Events
    const handleMouseMove = (e: React.MouseEvent) => {
        if (isResizingV) {
            const newWidth = (e.clientX / window.innerWidth) * 100;
            if (newWidth > 15 && newWidth < 85) {
                setVideoWidth(newWidth);
            }
        }
        if (isResizingH) {
            const gridHeight = window.innerHeight - 50;
            const newHeight = ((window.innerHeight - e.clientY) / gridHeight) * 100;
            if (newHeight > 10 && newHeight < 80) {
                setConsoleHeight(newHeight);
            }
        }
    };

    const handleMouseUp = () => {
        setIsResizingV(false);
        setIsResizingH(false);
    };

    // AI Chat endpoint handler
    const sendAiMessage = (e: React.FormEvent) => {
        e.preventDefault();
        if (!aiInput.trim() || aiSending) return;

        const userMsg = aiInput.trim();
        const updatedMessages = [...aiMessages, { role: 'user' as const, content: userMsg }];
        setAiMessages(updatedMessages);
        setAiInput('');
        setAiSending(true);

        fetch(`/api/courses/${slug}/ai-chat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
            },
            body: JSON.stringify({ messages: updatedMessages }),
        })
        .then(res => res.json())
        .then(data => {
            setAiMessages(prev => [...prev, { role: 'assistant', content: data.content }]);
            setAiSending(false);
        })
        .catch(err => {
            setAiMessages(prev => [...prev, { role: 'assistant', content: 'Sorry, I couldn\'t fetch a response.' }]);
            setAiSending(false);
        });
    };

    // Submit Comments
    const postComment = (e: React.FormEvent) => {
        e.preventDefault();
        newCommentForm.post(`/courses/${slug}/comments`, {
            preserveScroll: true,
            onSuccess: () => newCommentForm.reset(),
        });
    };

    const postReply = (e: React.FormEvent, commentId: number) => {
        e.preventDefault();
        replyForm.post(`/courses/${slug}/comments/${commentId}/reply`, {
            preserveScroll: true,
            onSuccess: () => {
                replyForm.reset();
                setReplyingCommentId(null);
            },
        });
    };

    const likeComment = (commentId: number) => {
        router.post(`/courses/${slug}/comments/${commentId}/like`, {}, {
            preserveScroll: true,
        });
    };

    const deleteComment = (commentId: number) => {
        if (confirm('Delete this comment?')) {
            router.delete(`/courses/${slug}/comments/${commentId}`, {
                preserveScroll: true,
            });
        }
    };

    const startEditing = (comment: Comment | Reply) => {
        setEditingCommentId(comment.id);
        editForm.setData('content', comment.content);
    };

    const saveEdit = (e: React.FormEvent, commentId: number) => {
        e.preventDefault();
        editForm.put(`/courses/${slug}/comments/${commentId}`, {
            preserveScroll: true,
            onSuccess: () => setEditingCommentId(null),
        });
    };

    return (
        <DashboardLayout title={course.title}>
            <Head>
                <style dangerouslySetInnerHTML={{__html: `
                    ::-webkit-scrollbar { display: none !important; }
                    * { scrollbar-width: none !important; -ms-overflow-style: none !important; }
                    
                    .learn-wrapper { display: flex; background: #fff; width: 100%; height: calc(100vh - 80px); }
                    .learn-main-col { flex: 1; min-width: 600px; padding: 30px; overflow-y: auto; border-right: 1px solid #edeff1; height: 100%; text-align: left; }
                    .learn-side-col { width: 300px; flex-shrink: 0; padding: 25px; background: #fafafa; overflow-y: auto; height: 100%; text-align: left; }
                    
                    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #888; font-weight: 600; margin-bottom: 24px; }
                    .breadcrumb span { color: #1c1c1c; background: #f0f2f5; padding: 4px 10px; border-radius: 20px; }

                    .course-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; gap: 20px; }
                    .course-title { font-size: 2rem; font-weight: 800; color: #1c1c1c; line-height: 1.2; }
                    .action-row { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
                    .icon-btn { width: 38px; height: 38px; border-radius: 8px; border: 1px solid #edeff1; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #555; background: #fff; cursor: pointer; }
                    .share-btn { padding: 0 16px; height: 38px; border-radius: 8px; background: var(--brand-primary); color: #fff; font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 6px; border: none; cursor: pointer; }

                    .meta-row { display: flex; align-items: center; gap: 20px; margin-bottom: 24px; }
                    .meta-item { display: flex; align-items: center; gap: 6px; font-size: 1.2rem; color: #666; font-weight: 600; }

                    .video-box { width: 100%; aspect-ratio: 16/9; min-height: 300px; background: #111; border-radius: 16px; overflow: hidden; margin-bottom: 24px; position: relative; }
                    .video-box iframe, .video-box #player { width: 100%; height: 100%; border: none; position: absolute; top: 0; left: 0; }

                    .tabs { display: flex; gap: 24px; border-bottom: 1px solid #f2f2f2; margin-bottom: 24px; }
                    .tab-item { padding-bottom: 10px; font-size: 1.3rem; font-weight: 700; color: #888; cursor: pointer; position: relative; display: flex; align-items: center; gap: 6px; user-select: none; }
                    .tab-item.active { color: var(--brand-primary); }
                    .tab-item.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background: var(--brand-primary); }

                    .content-section { margin-bottom: 30px; }
                    .section-label { font-size: 1.6rem; font-weight: 800; color: #1c1c1c; margin-bottom: 12px; }
                    .section-text { font-size: 1.4rem; line-height: 1.6; color: #555; }
                    
                    .concepts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; margin-top: 16px; }
                    .concept-card { background: #fbfbfb; border: 1px solid #f0f0f0; border-radius: 10px; padding: 14px; display: flex; align-items: center; gap: 10px; }
                    .concept-card ion-icon { font-size: 1.6rem; color: #23a55a; }
                    .concept-card p { font-size: 1.25rem; font-weight: 600; color: #444; }

                    .resource-card { display: flex; align-items: center; justify-content: space-between; padding: 14px; border: 1px solid #efefef; border-radius: 12px; margin-bottom: 12px; background: #fafafa; text-decoration: none; color: inherit; }
                    .resource-left { display: flex; align-items: center; gap: 12px; }
                    .resource-icon { width: 40px; height: 40px; border-radius: 8px; background: #f0f4f8; color: var(--brand-primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
                    .resource-info h4 { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; margin-bottom: 4px; }
                    .resource-info p { font-size: 1.15rem; color: #888; }

                    .comment-box { margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #efefef; display: flex; gap: 14px; }
                    .comment-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
                    .comment-content h5 { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
                    .comment-content h5 span { font-size: 1.05rem; font-weight: 500; color: #999; }
                    .comment-content p { font-size: 1.25rem; color: #444; line-height: 1.5; margin-bottom: 8px; }
                    .comment-actions { display: flex; gap: 16px; font-size: 1.15rem; color: #777; font-weight: 600; }
                    .comment-actions button { background: none; border: none; padding: 0; color: inherit; font: inherit; cursor: pointer; display: flex; align-items: center; gap: 6px; }
                    .comment-actions button:hover { color: var(--brand-primary); }
                    .comment-input-area { display: flex; gap: 14px; margin-bottom: 30px; }
                    .comment-input-area input { flex: 1; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 1.25rem; outline: none; }
                    .comment-input-area input:focus { border-color: var(--brand-primary); }
                    .comment-input-area button { padding: 0 24px; border-radius: 8px; border: none; background: var(--brand-primary); color: #fff; font-size: 1.2rem; font-weight: 700; cursor: pointer; }

                    .progress-widget { background: #fff; border: 1px solid #edeff1; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
                    .radial-lg { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; }
                    .radial-lg .inner-circle { width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 800; color: #1c1c1c; z-index: 2; }
                    .prog-info h4 { font-size: 1.35rem; font-weight: 800; color: #1c1c1c; margin-bottom: 2px; }
                    .prog-info p { font-size: 1.15rem; color: #888; font-weight: 500; line-height: 1.3; }

                    .curriculum-list { display: flex; flex-direction: column; gap: 10px; }
                    .curr-item { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: 0.2s; }
                    .curr-item:hover { transform: translateX(3px); border-color: var(--brand-primary); }
                    .curr-item.active { border: 1px solid var(--brand-primary); background: #fdf8f6; box-shadow: 0 4px 12px rgba(255, 69, 0, 0.08); }
                    .curr-left { display: flex; align-items: center; gap: 12px; }
                    .curr-num { width: 28px; height: 28px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 700; color: #666; }
                    .curr-item.active .curr-num { background: var(--brand-primary); color: #fff; }
                    .curr-info h5 { font-size: 1.25rem; font-weight: 700; color: #1c1c1c; margin-bottom: 2px; }
                    .curr-info span { font-size: 1.1rem; color: #999; }
                    .radial-sm { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; }
                    .radial-sm::after { content: ''; position: absolute; width: 14px; height: 14px; background: #fff; border-radius: 50%; }
                    .curr-item.active .radial-sm::after { background: #fdf8f6; }

                    @media (max-width: 1200px) {
                        .learn-wrapper { flex-direction: column; height: auto; }
                        .learn-main-col { border-right: none; height: auto; overflow: visible; min-width: 0; width: 100%; }
                        .learn-side-col { width: 100%; background: #fff; border-top: 1px solid #edeff1; height: auto; overflow: visible; }
                    }
                    @media (max-width: 768px) {
                        .course-header { flex-direction: column; align-items: flex-start; gap: 16px; }
                        .action-row { width: 100%; justify-content: flex-start; }
                    }

                    /* CODE WORKSPACE WINDOW */
                    .code-workspace { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #1e1e1e; z-index: 99999; display: flex; flex-direction: column; font-family: 'Inter', sans-serif; }
                    .workspace-header { height: 50px; background: #181818; border-bottom: 1px solid #2d2d2d; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; color: #fff; }
                    .workspace-title { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 1.3rem; color: #ccc; }
                    .workspace-title ion-icon { color: #ff4500; font-size: 1.6rem; }
                    .workspace-btn { background: #ff4500; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 6px; cursor: pointer; }
                    .workspace-btn:hover { background: #e03e00; }
                    .workspace-grid { flex: 1; display: grid; height: calc(100vh - 50px); overflow: hidden; }
                    .workspace-video-area { background: #000; position: relative; overflow: hidden; height: 100%; }
                    .v-resizer { background: #252526; cursor: col-resize; z-index: 10; border-left: 1px solid #111; border-right: 1px solid #333; }
                    .v-resizer:hover, .v-resizer.dragging { background: #ff4500; }
                    .h-resizer { background: #252526; cursor: row-resize; z-index: 10; border-top: 1px solid #111; border-bottom: 1px solid #333; }
                    .h-resizer:hover, .h-resizer.dragging { background: #ff4500; }
                    .workspace-editor-area { background: #1e1e1e; display: flex; min-width: 0; height: 100%; }
                    .vscode-activity-bar { width: 48px; height: 100%; background: #181818; border-right: 1px solid #2d2d2d; display: flex; flex-direction: column; align-items: center; padding-top: 8px; gap: 4px; }
                    .activity-bar-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #858585; font-size: 1.5rem; cursor: pointer; border-radius: 6px; border-left: 2px solid transparent; }
                    .activity-bar-icon.active { color: #fff; border-left-color: #ff4500; background: rgba(255, 69, 0, 0.08); }
                    .vscode-sidebar { width: 220px; height: 100%; background: #181818; border-right: 1px solid #2d2d2d; display: flex; flex-direction: column; overflow: hidden; transition: width 0.2s; }
                    .vscode-sidebar.collapsed { width: 0; border-right: none; }
                    .sidebar-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; color: #bbb; font-size: 0.85rem; font-weight: 700; white-space: nowrap; }
                    .file-item { display: flex; align-items: center; gap: 8px; padding: 5px 15px 5px 25px; color: #ccc; font-size: 1rem; cursor: pointer; }
                    .file-item.active { background: #37373d; color: #fff; }
                    .vscode-main { flex: 1; height: 100%; display: flex; flex-direction: column; overflow: hidden; background: #1e1e1e; min-width: 0; }
                    .editor-tabs { height: 38px; background: #1e1e1e; display: flex; }
                    .editor-tab { display: flex; align-items: center; gap: 8px; padding: 0 15px; background: #2d2d2d; color: #999; font-size: 1rem; border-right: 1px solid #1e1e1e; border-top: 2px solid transparent; }
                    .editor-tab.active { background: #1e1e1e; color: #fff; border-top: 2px solid #ff4500; }
                    .editor-container { flex: 1; position: relative; min-height: 0; min-width: 0; }
                    .workspace-console-area { background: #1e1e1e; display: flex; flex-direction: column; overflow: hidden; }
                    .console-header { height: 35px; background: #1e1e1e; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; color: #e7e7e7; font-size: 1rem; font-weight: 600; border-bottom: 1px solid #2d2d2d; }
                    .console-tabs { display: flex; gap: 20px; }
                    .console-header-tab { color: #999; font-size: 0.95rem; cursor: pointer; padding-bottom: 8px; border-bottom: 1px solid transparent; }
                    .console-header-tab.active { color: #e7e7e7; border-bottom: 1px solid #ff4500; }
                    .clear-console-btn { background: none; border: none; color: #888; cursor: pointer; font-size: 1.4rem; }
                    .console-body { flex: 1; padding: 15px 20px; font-family: 'Fira Code', monospace; font-size: 1rem; color: #d4d4d4; overflow-y: auto; text-align: left; }
                    .console-line { margin-bottom: 6px; line-height: 1.4; }
                    .console-line.success { color: #4caf50; }
                    .console-prompt { color: #ff4500; margin-right: 8px; }
                `}} />
            </Head>

            <div className="learn-wrapper">
                {/* Left/Main Column */}
                <div className="learn-main-col">
                    <nav className="breadcrumb">
                        <Link href="/courses" style={{ textDecoration: 'none', color: 'inherit' }}>Courses</Link>
                        <ion-icon name="chevron-forward"></ion-icon>
                        {course.category}
                        <ion-icon name="chevron-forward"></ion-icon>
                        <span>{course.title}</span>
                    </nav>

                    <header className="course-header">
                        <h1 className="course-title">{course.title}</h1>
                        <div className="action-row">
                            <button className="icon-btn" onClick={() => setWorkspaceOpen(true)} title="Open Code Workspace">
                                <ion-icon name="code-slash-outline"></ion-icon>
                            </button>
                            <button className="share-btn" onClick={() => {
                                if (navigator.share) {
                                    navigator.share({ title: course.title, text: 'Check this out on SkillUp!', url: window.location.href });
                                } else {
                                    navigator.clipboard.writeText(window.location.href);
                                    alert('Link copied to clipboard!');
                                }
                            }}>
                                <ion-icon name="share-social-outline"></ion-icon> Share
                            </button>
                            <img 
                                src="/fitlife-assets/images/ai-icon.png" 
                                onClick={() => setAiChatOpen(!aiChatOpen)}
                                style={{ width: '44px', height: '44px', borderRadius: '10px', marginLeft: '10px', cursor: 'pointer', transition: '0.2s' }} 
                                onMouseOver={e => e.currentTarget.style.transform = 'scale(1.05)'}
                                onMouseOut={e => e.currentTarget.style.transform = 'scale(1)'}
                                alt="AI Tutor" 
                                title="Ask AI Tutor"
                            />
                        </div>
                    </header>

                    <div className="meta-row">
                        <div className="meta-item"><ion-icon name="ribbon-outline"></ion-icon> {course.level}</div>
                        <div className="meta-item"><ion-icon name="list-outline"></ion-icon> {course.lessons_count} Lessons</div>
                        <div className="meta-item"><ion-icon name="time-outline"></ion-icon> {course.duration}</div>
                    </div>

                    <div className="video-box">
                        <div id="player"></div>
                    </div>

                    <div className="tabs">
                        <div className={`tab-item ${activeTab === 'summary' ? 'active' : ''}`} onClick={() => setActiveTab('summary')}>
                            <ion-icon name="document-text-outline"></ion-icon> Summary
                        </div>
                        <div className={`tab-item ${activeTab === 'files' ? 'active' : ''}`} onClick={() => setActiveTab('files')}>
                            <ion-icon name="folder-open-outline"></ion-icon> Files
                        </div>
                        <div className={`tab-item ${activeTab === 'resources' ? 'active' : ''}`} onClick={() => setActiveTab('resources')}>
                            <ion-icon name="link-outline"></ion-icon> Resources
                        </div>
                        <div className={`tab-item ${activeTab === 'comments' ? 'active' : ''}`} onClick={() => setActiveTab('comments')}>
                            <ion-icon name="chatbubble-ellipses-outline"></ion-icon> Comments
                        </div>
                    </div>

                    <div className="tab-content">
                        {activeTab === 'summary' && (
                            <div>
                                <section className="content-section">
                                    <h2 className="section-label">Lesson Recap</h2>
                                    <div className="section-text">{course.recap}</div>
                                </section>
                                <section className="content-section">
                                    <h2 className="section-label">Key Concepts</h2>
                                    <div className="concepts-grid">
                                        {course.concepts.map((concept, idx) => (
                                            <div key={idx} className="concept-card">
                                                <ion-icon name="checkmark-circle"></ion-icon>
                                                <p>{concept}</p>
                                            </div>
                                        ))}
                                    </div>
                                </section>
                            </div>
                        )}

                        {activeTab === 'files' && (
                            <section className="content-section">
                                <h2 className="section-label">Source Code & Assets</h2>
                                {course.source_files_url ? (
                                    <a href={course.source_files_url} target="_blank" rel="noreferrer" className="resource-card">
                                        <div className="resource-left">
                                            <div className="resource-icon"><ion-icon name="logo-github"></ion-icon></div>
                                            <div className="resource-info">
                                                <h4>Source Files</h4>
                                                <p>Open in new tab</p>
                                            </div>
                                        </div>
                                        <ion-icon name="open-outline" style={{ fontSize: '1.8rem', color: '#bbb' }}></ion-icon>
                                    </a>
                                ) : (
                                    <p style={{ color: '#888', fontSize: '1.3rem' }}>No source files provided for this course yet.</p>
                                )}
                            </section>
                        )}

                        {activeTab === 'resources' && (
                            <section className="content-section">
                                <h2 className="section-label">Helpful Links</h2>
                                {course.cheatsheet_url ? (
                                    <a href={course.cheatsheet_url} target="_blank" rel="noreferrer" className="resource-card">
                                        <div className="resource-left">
                                            <div className="resource-icon" style={{ background: '#fdfcf0', color: '#e5b300' }}><ion-icon name="book-outline"></ion-icon></div>
                                            <div className="resource-info">
                                                <h4>Cheat Sheet / Documentation</h4>
                                                <p>Read the official guides.</p>
                                            </div>
                                        </div>
                                        <ion-icon name="open-outline" style={{ fontSize: '1.8rem', color: '#bbb' }}></ion-icon>
                                    </a>
                                ) : (
                                    <p style={{ color: '#888', fontSize: '1.3rem' }}>No additional resources provided for this course yet.</p>
                                )}
                            </section>
                        )}

                        {activeTab === 'comments' && (
                            <div>
                                <h2 className="section-label" style={{ marginBottom: '20px' }}>Discussion</h2>
                                
                                {/* Comment Input Box */}
                                <form onSubmit={postComment} className="comment-input-area">
                                    <input 
                                        type="text" 
                                        placeholder="Add to the discussion..." 
                                        value={newCommentForm.data.content}
                                        onChange={e => newCommentForm.setData('content', e.target.value)}
                                        required
                                    />
                                    <button type="submit" disabled={newCommentForm.processing}>Comment</button>
                                </form>

                                {/* List of Comments */}
                                {comments.map((comment) => (
                                    <div key={comment.id} className="comment-box">
                                        <img src={comment.avatar} alt={comment.user_name} className="comment-avatar" />
                                        <div className="comment-content" style={{ flexGrow: 1, textAlign: 'left' }}>
                                            <h5>
                                                {comment.user_name} 
                                                <span>{new Date(comment.created_at).toLocaleDateString()}</span>
                                            </h5>
                                            
                                            {editingCommentId === comment.id ? (
                                                <form onSubmit={(e) => saveEdit(e, comment.id)} style={{ display: 'flex', gap: '10px', marginTop: '10px' }}>
                                                    <input 
                                                        type="text" 
                                                        value={editForm.data.content}
                                                        onChange={e => editForm.setData('content', e.target.value)}
                                                        className="form-control"
                                                        style={{ flexGrow: 1, padding: '8px 12px', border: '1px solid #ddd', borderRadius: '6px' }}
                                                        required
                                                    />
                                                    <button type="submit" style={{ background: '#1c1c28', color: '#fff', border: 'none', padding: '6px 12px', borderRadius: '6px' }}>Save</button>
                                                    <button type="button" onClick={() => setEditingCommentId(null)} style={{ background: '#fff', border: '1px solid #ddd', padding: '6px 12px', borderRadius: '6px' }}>Cancel</button>
                                                </form>
                                            ) : (
                                                <p>{comment.content}</p>
                                            )}

                                            <div className="comment-actions">
                                                <button onClick={() => likeComment(comment.id)} style={{ color: comment.is_liked ? 'var(--brand-primary)' : 'inherit' }}>
                                                    <ion-icon name={comment.is_liked ? "thumbs-up" : "thumbs-up-outline"}></ion-icon> {comment.likes}
                                                </button>
                                                <button onClick={() => setReplyingCommentId(replyingCommentId === comment.id ? null : comment.id)}>
                                                    <ion-icon name="chatbubble-outline"></ion-icon> Reply
                                                </button>
                                                {auth.user && auth.user.id === comment.user_id && (
                                                    <>
                                                        <button onClick={() => startEditing(comment)}><ion-icon name="create-outline"></ion-icon> Edit</button>
                                                        <button onClick={() => deleteComment(comment.id)}><ion-icon name="trash-outline"></ion-icon> Delete</button>
                                                    </>
                                                )}
                                            </div>

                                            {/* Reply Input Area */}
                                            {replyingCommentId === comment.id && (
                                                <form onSubmit={(e) => postReply(e, comment.id)} style={{ display: 'flex', gap: '10px', marginTop: '15px' }}>
                                                    <input 
                                                        type="text" 
                                                        placeholder="Write a reply..." 
                                                        value={replyForm.data.content}
                                                        onChange={e => replyForm.setData('content', e.target.value)}
                                                        style={{ flexGrow: 1, padding: '8px 12px', border: '1px solid #ddd', borderRadius: '6px' }}
                                                        required
                                                    />
                                                    <button type="submit" style={{ background: 'var(--brand-primary)', color: '#fff', border: 'none', padding: '6px 15px', borderRadius: '6px', fontWeight: 'bold' }}>Reply</button>
                                                </form>
                                            )}

                                            {/* Nested Replies */}
                                            {comment.replies.map((reply) => (
                                                <div key={reply.id} className="comment-box" style={{ marginTop: '20px', borderBottom: 'none', paddingBottom: 0 }}>
                                                    <img src={reply.avatar} alt={reply.user_name} className="comment-avatar" style={{ width: '32px', height: '32px' }} />
                                                    <div className="comment-content" style={{ flexGrow: 1 }}>
                                                        <h5>
                                                            {reply.user_name} 
                                                            <span>{new Date(reply.created_at).toLocaleDateString()}</span>
                                                        </h5>
                                                        
                                                        {editingCommentId === reply.id ? (
                                                            <form onSubmit={(e) => saveEdit(e, reply.id)} style={{ display: 'flex', gap: '10px', marginTop: '10px' }}>
                                                                <input 
                                                                    type="text" 
                                                                    value={editForm.data.content}
                                                                    onChange={e => editForm.setData('content', e.target.value)}
                                                                    className="form-control"
                                                                    style={{ flexGrow: 1, padding: '8px 12px', border: '1px solid #ddd', borderRadius: '6px' }}
                                                                    required
                                                                />
                                                                <button type="submit" style={{ background: '#1c1c28', color: '#fff', border: 'none', padding: '6px 12px', borderRadius: '6px' }}>Save</button>
                                                                <button type="button" onClick={() => setEditingCommentId(null)} style={{ background: '#fff', border: '1px solid #ddd', padding: '6px 12px', borderRadius: '6px' }}>Cancel</button>
                                                            </form>
                                                        ) : (
                                                            <p>{reply.content}</p>
                                                        )}

                                                        <div className="comment-actions">
                                                            <button onClick={() => likeComment(reply.id)} style={{ color: reply.is_liked ? 'var(--brand-primary)' : 'inherit' }}>
                                                                <ion-icon name={reply.is_liked ? "thumbs-up" : "thumbs-up-outline"}></ion-icon> {reply.likes}
                                                            </button>
                                                            {auth.user && auth.user.id === reply.user_id && (
                                                                <>
                                                                    <button onClick={() => startEditing(reply)}><ion-icon name="create-outline"></ion-icon> Edit</button>
                                                                    <button onClick={() => deleteComment(reply.id)}><ion-icon name="trash-outline"></ion-icon> Delete</button>
                                                                </>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Right/Curriculum Sidebar */}
                <div className="learn-side-col">
                    <div className="section-label" style={{ fontWeight: 'bold' }}>Course Content</div>
                    
                    {(() => {
                        const totalProgress = lessons.reduce((acc, curr) => acc + curr.progress, 0);
                        const overallProgress = lessons.length > 0 ? Math.round(totalProgress / lessons.length) : 0;
                        return (
                            <div className="progress-widget">
                                <div className="radial-lg" style={{ background: `conic-gradient(var(--brand-primary) ${overallProgress}%, #eee 0)` }}>
                                    <div className="inner-circle">{overallProgress}%</div>
                                </div>
                                <div className="prog-info">
                                    <h4>Study Progress</h4>
                                    <p>Track your learning milestones and where you left off.</p>
                                </div>
                            </div>
                        );
                    })()}

                    <div className="curriculum-list">
                        {lessons.map((lesson, idx) => (
                            <Link key={lesson.video_id} href={`?v=${lesson.video_id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
                                <div className={`curr-item ${lesson.active ? 'active' : ''}`}>
                                    <div className="curr-left">
                                        <div className="curr-num">{idx + 1}</div>
                                        <div className="curr-info">
                                            <h5>{lesson.title}</h5>
                                            <span>{lesson.time}</span>
                                        </div>
                                    </div>
                                    <div className="radial-sm" style={{ background: `conic-gradient(var(--brand-primary) ${lesson.progress}%, #eee 0)` }}></div>
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>
            </div>

            {/* AI TUTOR CHAT DRAWER */}
            {aiChatOpen && (
                <div style={{ position: 'fixed', right: 0, top: 0, width: '400px', height: '100vh', background: '#fff', boxShadow: '-5px 0 25px rgba(0,0,0,0.15)', zIndex: 10000, display: 'flex', flexDirection: 'column', borderLeft: '1px solid #eaeaea', textAlign: 'left' }}>
                    <div style={{ padding: '20px', background: '#1c1c28', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <img src="/fitlife-assets/images/ai-icon.png" style={{ width: '36px', height: '36px', borderRadius: '8px' }} alt="AI" />
                            <div>
                                <h3 style={{ fontSize: '1.4rem', fontWeight: 800, margin: 0 }}>AI Study Buddy</h3>
                                <p style={{ fontSize: '1rem', color: '#ccc', margin: 0 }}>Ask me anything</p>
                            </div>
                        </div>
                        <button onClick={() => setAiChatOpen(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '2.4rem', cursor: 'pointer' }}>
                            <ion-icon name="close-outline"></ion-icon>
                        </button>
                    </div>

                    <div style={{ flex: 1, padding: '20px', overflowY: 'auto', display: 'flex', flexDirection: 'column', gap: '15px' }}>
                        {aiMessages.map((msg, idx) => (
                            <div 
                                key={idx} 
                                style={{ 
                                    alignSelf: msg.role === 'user' ? 'flex-end' : 'flex-start',
                                    background: msg.role === 'user' ? 'var(--brand-primary)' : '#f0f2f5',
                                    color: msg.role === 'user' ? '#fff' : '#1c1c1c',
                                    padding: '12px 16px',
                                    borderRadius: msg.role === 'user' ? '18px 18px 0 18px' : '18px 18px 18px 0',
                                    maxWidth: '85%',
                                    fontSize: '1.3rem',
                                    lineHeight: 1.5,
                                    whiteSpace: 'pre-line',
                                    boxShadow: '0 2px 5px rgba(0,0,0,0.03)'
                                }}
                            >
                                {msg.content}
                            </div>
                        ))}
                        {aiSending && (
                            <div style={{ alignSelf: 'flex-start', background: '#f0f2f5', padding: '12px 16px', borderRadius: '18px 18px 18px 0', fontSize: '1.3rem', color: '#888' }}>
                                AI is thinking...
                            </div>
                        )}
                        <div ref={aiChatEndRef} />
                    </div>

                    <form onSubmit={sendAiMessage} style={{ padding: '15px', borderTop: '1px solid #eaeaea', display: 'flex', gap: '10px' }}>
                        <input 
                            type="text" 
                            placeholder="Ask a question about this course..." 
                            value={aiInput}
                            onChange={e => setAiInput(e.target.value)}
                            style={{ flex: 1, padding: '12px 16px', border: '1px solid #ddd', borderRadius: '25px', outline: 'none', fontSize: '1.25rem' }}
                            required
                            disabled={aiSending}
                        />
                        <button type="submit" disabled={aiSending} style={{ width: '40px', height: '40px', borderRadius: '50%', background: 'var(--brand-primary)', color: '#fff', border: 'none', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '1.8rem' }}>
                            <ion-icon name="send"></ion-icon>
                        </button>
                    </form>
                </div>
            )}

            {/* MONACO CODE WORKSPACE OVERLAY */}
            {workspaceOpen && (
                <div 
                    className="code-workspace"
                    onMouseMove={handleMouseMove}
                    onMouseUp={handleMouseUp}
                >
                    <div className="workspace-header">
                        <div className="workspace-title">
                            <ion-icon name="code-slash-outline"></ion-icon> SkillUp Workspace
                        </div>
                        <div className="workspace-actions">
                            <button className="workspace-btn" onClick={() => setWorkspaceOpen(false)}>
                                <ion-icon name="close-outline"></ion-icon> Exit Workspace
                            </button>
                        </div>
                    </div>

                    <div className="workspace-grid" style={{ gridTemplateColumns: `calc(${videoWidth}% - 2px) 4px 1fr`, gridTemplateRows: `1fr 4px calc(${consoleHeight}% - 2px)` }}>
                        {/* Video Player Box */}
                        <div className="workspace-video-area" id="workspace-video-container"></div>

                        {/* Split Resizer V */}
                        <div className="v-resizer" onMouseDown={() => setIsResizingV(true)}></div>

                        {/* Monaco Editor Panel */}
                        <div className="workspace-editor-area">
                            <div className="vscode-activity-bar">
                                <div className={`activity-bar-icon ${!sidebarCollapsed ? 'active' : ''}`} onClick={() => setSidebarCollapsed(!sidebarCollapsed)}>
                                    <ion-icon name="copy-outline"></ion-icon>
                                </div>
                                <div className="activity-bar-icon"><ion-icon name="search-outline"></ion-icon></div>
                                <div className="activity-bar-icon"><ion-icon name="git-branch-outline"></ion-icon></div>
                            </div>

                            <div className={`vscode-sidebar ${sidebarCollapsed ? 'collapsed' : ''}`}>
                                <div className="sidebar-header">
                                    <span>EXPLORER</span>
                                </div>
                                <div className="sidebar-files">
                                    <div className="file-item active">
                                        <ion-icon name="logo-html5" style={{ color: '#e34f26' }}></ion-icon> index.html
                                    </div>
                                    <div className="file-item">
                                        <ion-icon name="logo-css3" style={{ color: '#1572B6' }}></ion-icon> style.css
                                    </div>
                                    <div className="file-item">
                                        <ion-icon name="logo-javascript" style={{ color: '#f7df1e' }}></ion-icon> script.js
                                    </div>
                                </div>
                            </div>

                            <div className="vscode-main">
                                <div className="editor-tabs">
                                    <div className="editor-tab active">
                                        <ion-icon name="logo-html5" style={{ color: '#e34f26' }}></ion-icon> index.html
                                    </div>
                                </div>
                                <div className="editor-container">
                                    <div id="monaco-editor-container" style={{ position: 'absolute', inset: 0 }}></div>
                                </div>
                            </div>
                        </div>

                        {/* Split Resizer H */}
                        <div className="h-resizer" style={{ gridColumn: '1 / -1' }} onMouseDown={() => setIsResizingH(true)}></div>

                        {/* Console Panel */}
                        <div className="workspace-console-area" style={{ gridColumn: '1 / -1' }}>
                            <div className="console-header">
                                <div className="console-tabs">
                                    <div className="console-header-tab active">OUTPUT</div>
                                    <div className="console-header-tab">TERMINAL</div>
                                </div>
                                <button className="clear-console-btn" onClick={() => setConsoleLogs(['~/skillup/project$'])}>
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </div>
                            <div className="console-body">
                                {consoleLogs.map((log, idx) => (
                                    <div key={idx} className={`console-line ${log.startsWith('VITE') ? 'success' : ''}`}>
                                        {!log.startsWith('VITE') && !log.startsWith(' ') && log !== '' && <span className="console-prompt">~/skillup/project$</span>}
                                        {log}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
