@extends('layouts.dashboard')

@section('title', $course['title'])

@push('styles')
    <style>
        :root {
            --brand-primary: #ff4500 !important;
            --border-color: #edeff1 !important;
        }
        /* Force layout overrides */
        body { overflow: hidden !important; }
        body .layout-wrapper { margin: 0 !important; width: 100% !important; max-width: none !important; transition: padding-left 0.3s ease !important; }
        body .layout-wrapper:not(.sidebar-collapsed) { padding-left: 260px !important; }
        body .layout-wrapper.sidebar-collapsed { padding-left: 80px !important; }
        
        @media (max-width: 992px) { 
            body .layout-wrapper,
            body .layout-wrapper:not(.sidebar-collapsed),
            body .layout-wrapper.sidebar-collapsed { padding-left: 0 !important; } 
        }

        /* Kill the red scrollbar / Hide all scrollbars while maintaining scroll functionality */
        ::-webkit-scrollbar { display: none !important; }
        * { scrollbar-width: none !important; -ms-overflow-style: none !important; }

        body .layout-wrapper .main-col { width: 100% !important; max-width: none !important; margin: 0 !important; }
        body .layout-wrapper .content-area { 
            padding: 0 !important; 
            margin: 0 !important;
            background: #fff !important;
            height: calc(100vh - 80px) !important;
            max-width: none !important;
            position: relative !important;
        }
        
        .learn-wrapper { 
            display: flex;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 50;
        }

        @media (max-width: 1200px) {
            body { overflow: auto !important; }
            .learn-wrapper { position: relative; z-index: 10; }
        }
        
        /* Learn Main Content */
        .learn-main-col { flex: 1; min-width: 600px; padding: 30px; overflow-y: auto; border-right: 1px solid var(--border-color); height: 100%; }
        
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #888; font-weight: 600; margin-bottom: 24px; white-space: nowrap; overflow: hidden; }
        .breadcrumb ion-icon { font-size: 1.4rem; flex-shrink: 0; }
        .breadcrumb span { color: #1c1c1c; background: #f0f2f5; padding: 4px 10px; border-radius: 20px; text-overflow: ellipsis; overflow: hidden; }
        .breadcrumb a:hover { color: var(--brand-primary); }

        .course-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; gap: 20px; }
        .course-title { font-size: 2rem; font-weight: 800; color: #1c1c1c; line-height: 1.2; }
        
        .action-row { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .icon-btn { width: 38px; height: 38px; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #555; background: #fff; cursor: pointer; }
        .share-btn { padding: 0 16px; height: 38px; border-radius: 8px; background: var(--brand-primary); color: #fff; font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 6px; border: none; cursor: pointer; }

        .meta-row { display: flex; align-items: center; gap: 20px; margin-bottom: 24px; }
        .meta-item { display: flex; align-items: center; gap: 6px; font-size: 1.2rem; color: #666; font-weight: 600; }
        .meta-item ion-icon { font-size: 1.6rem; color: #888; }

        .video-box { width: 100%; aspect-ratio: 16/9; min-height: 300px; background: #111; border-radius: 16px; overflow: hidden; margin-bottom: 24px; position: relative; z-index: 5; }
        .video-box iframe { width: 100%; height: 100%; border: none; position: absolute; top: 0; left: 0; }

        .tabs { display: flex; gap: 24px; border-bottom: 1px solid #f2f2f2; margin-bottom: 24px; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 5px; scrollbar-width: none; }
        .tabs::-webkit-scrollbar { display: none; }
        .tab-item { padding-bottom: 10px; font-size: 1.3rem; font-weight: 700; color: #888; cursor: pointer; position: relative; display: flex; align-items: center; gap: 6px; white-space: nowrap; user-select: none; }
        .tab-item.active { color: var(--brand-primary); }
        .tab-item.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background: var(--brand-primary); }
        
        .tab-pane { display: none; animation: fadeIn 0.3s ease; }
        .tab-pane.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        .content-section { margin-bottom: 30px; }
        .section-label { font-size: 1.6rem; font-weight: 800; color: #1c1c1c; margin-bottom: 12px; }
        .section-text { font-size: 1.4rem; line-height: 1.6; color: #555; }
        
        .concepts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; margin-top: 16px; }
        .concept-card { background: #fbfbfb; border: 1px solid #f0f0f0; border-radius: 10px; padding: 14px; display: flex; align-items: center; gap: 10px; }
        .concept-card ion-icon { font-size: 1.6rem; color: #23a55a; flex-shrink: 0; }
        .concept-card p { font-size: 1.25rem; font-weight: 600; color: #444; line-height: 1.3; }

        /* Files & Resources Styles */
        .resource-card { display: flex; align-items: center; justify-content: space-between; padding: 14px; border: 1px solid #efefef; border-radius: 12px; margin-bottom: 12px; background: #fafafa; }
        .resource-left { display: flex; align-items: center; gap: 12px; }
        .resource-icon { width: 40px; height: 40px; border-radius: 8px; background: #f0f4f8; color: var(--brand-primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
        .resource-info h4 { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; margin-bottom: 4px; }
        .resource-info p { font-size: 1.15rem; color: #888; }
        .download-btn { padding: 6px 14px; font-size: 1.15rem; font-weight: 700; color: #1c1c1c; background: #fff; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: 0.2s; }
        .download-btn:hover { background: #f4f4f4; border-color: #ccc; }

        /* Comments Styles */
        .comment-box { margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #efefef; display: flex; gap: 14px; }
        .comment-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
        .comment-content h5 { font-size: 1.3rem; font-weight: 700; color: #1c1c1c; display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .comment-content h5 span { font-size: 1.05rem; font-weight: 500; color: #999; }
        .comment-content p { font-size: 1.25rem; color: #444; line-height: 1.5; margin-bottom: 8px; }
        .comment-actions { display: flex; gap: 16px; font-size: 1.15rem; color: #777; font-weight: 600; }
        .comment-actions button { background: none; border: none; padding: 0; color: inherit; font: inherit; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; }
        .comment-actions button:hover { color: var(--brand-primary); }
        .comment-input-area { display: flex; gap: 14px; margin-bottom: 30px; }
        .comment-input-area input { flex: 1; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 1.25rem; outline: none; }
        .comment-input-area input:focus { border-color: var(--brand-primary); }
        .comment-input-area button { padding: 0 24px; border-radius: 8px; border: none; background: var(--brand-primary); color: #fff; font-size: 1.2rem; font-weight: 700; cursor: pointer; }

        /* Learn Sidebar Column */
        .learn-side-col { width: 300px; flex-shrink: 0; padding: 25px; background: #fafafa; overflow-y: auto; height: 100%; }
        
        .progress-widget { background: #fff; border: 1px solid var(--border-color); border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .radial-lg { 
            width: 50px; height: 50px; border-radius: 50%; 
            background: conic-gradient(var(--brand-primary) var(--p), #eee 0); 
            display: flex; align-items: center; justify-content: center; position: relative;
        }
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
        .curr-info span { font-size: 1.1rem; color: #999; font-weight: 500; }

        .radial-sm { 
            width: 20px; height: 20px; border-radius: 50%; 
            background: conic-gradient(var(--brand-primary) var(--p), #eee 0); 
            display: flex; align-items: center; justify-content: center; position: relative;
        }
        .radial-sm::after { content: ''; position: absolute; width: 14px; height: 14px; background: #fff; border-radius: 50%; }
        .curr-item.active .radial-sm::after { background: #fdf8f6; }

        @media (max-width: 1366px) {
            .learn-side-col { padding: 20px; }
            .learn-main-col { padding: 20px; }
        }

        @media (max-width: 1200px) {
            .learn-wrapper { flex-direction: column; height: auto; }
            .learn-main-col { border-right: none; height: auto; overflow: visible; min-width: 0; width: 100%; }
            .learn-side-col { width: 100%; background: #fff; border-top: 1px solid var(--border-color); height: auto; overflow: visible; }
            
            body .layout-wrapper .content-area { height: auto !important; overflow: auto !important; }
        }

        @media (max-width: 768px) {
            .course-header { flex-direction: column; align-items: flex-start; gap: 16px; }
            .course-title { font-size: 1.8rem; }
            .action-row { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
            .learn-main-col { padding: 15px; }
            .learn-side-col { padding: 15px; }
            .meta-row { flex-wrap: wrap; gap: 10px; }
            .video-box { min-height: 200px; }
        }
    </style>
@endpush

@section('content')
    <div class="learn-wrapper">
        <!-- Main Panel -->
        <div class="learn-main-col">
            <nav class="breadcrumb">
                <a href="{{ route('courses') }}" style="text-decoration: none; color: inherit; transition: 0.2s;">Courses</a> <ion-icon name="chevron-forward"></ion-icon> 
                {{ $course['category'] }} <ion-icon name="chevron-forward"></ion-icon>
                <span>{{ $course['title'] }}</span>
            </nav>

            <header class="course-header">
                <h1 class="course-title">{{ $course['title'] }}</h1>
                <div class="action-row">
                    <button class="icon-btn"><ion-icon name="code-slash-outline"></ion-icon></button>
                    <button class="share-btn" onclick="shareCourse()"><ion-icon name="share-social-outline"></ion-icon> Share</button>
                    <img src="{{ asset('fitlife-assets/images/ai-icon.png') }}" onclick="Livewire.dispatch('toggleAiChat')" style="width: 44px; height: 44px; border-radius: 10px; margin-left: 10px; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="AI Tutor" title="Ask AI Tutor">
                </div>
            </header>

            <div class="meta-row">
                <div class="meta-item"><ion-icon name="ribbon-outline"></ion-icon> {{ $course['level'] }}</div>
                <div class="meta-item"><ion-icon name="list-outline"></ion-icon> {{ $course['lessons_count'] }} Lessons</div>
                <div class="meta-item"><ion-icon name="time-outline"></ion-icon> {{ $course['duration'] }}</div>
            </div>

            <div class="video-box">
                <div id="player"></div>
            </div>

            <div class="tabs">
                <div class="tab-item active" data-tab="summary"><ion-icon name="document-text-outline"></ion-icon> Summary</div>
                <div class="tab-item" data-tab="files"><ion-icon name="folder-open-outline"></ion-icon> Files</div>
                <div class="tab-item" data-tab="resources"><ion-icon name="link-outline"></ion-icon> Resources</div>
                <div class="tab-item" data-tab="comments"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Comments</div>
            </div>

            <div class="tab-content">
                <!-- SUMMARY TAB -->
                <div id="tab-summary" class="tab-pane active">
                    <section class="content-section">
                        <h2 class="section-label">Lesson Recap</h2>
                        <div class="section-text">
                            {{ $course['recap'] }}
                        </div>
                    </section>
        
                    <section class="content-section">
                        <h2 class="section-label">Key Concepts</h2>
                        <div class="concepts-grid">
                            @foreach($course['concepts'] as $concept)
                            <div class="concept-card">
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <p>{{ $concept }}</p>
                            </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <!-- FILES TAB -->
                <div id="tab-files" class="tab-pane">
                    <section class="content-section">
                        <h2 class="section-label">Source Code & Assets</h2>
                        @if(!empty($course['source_files_url']))
                        <a href="{{ $course['source_files_url'] }}" target="_blank" class="resource-card" style="text-decoration:none;color:inherit;display:flex;">
                            <div class="resource-left">
                                <div class="resource-icon"><ion-icon name="logo-github"></ion-icon></div>
                                <div class="resource-info">
                                    <h4>Source Files</h4>
                                    <p>Open in new tab</p>
                                </div>
                            </div>
                            <ion-icon name="open-outline" style="font-size:1.8rem;color:#bbb;"></ion-icon>
                        </a>
                        @else
                        <p style="color:var(--text-mut);font-size:1.3rem;">No source files provided for this course yet.</p>
                        @endif
                    </section>
                </div>

                <!-- RESOURCES TAB -->
                <div id="tab-resources" class="tab-pane">
                    <section class="content-section">
                        <h2 class="section-label">Helpful Links</h2>
                        @if(!empty($course['cheatsheet_url']))
                        <a href="{{ $course['cheatsheet_url'] }}" target="_blank" style="text-decoration:none;color:inherit;display:block;" class="resource-card">
                            <div class="resource-left">
                                <div class="resource-icon" style="background:#fdfcf0;color:#e5b300;"><ion-icon name="book-outline"></ion-icon></div>
                                <div class="resource-info">
                                    <h4>Cheat Sheet / Documentation</h4>
                                    <p>Read the official guides.</p>
                                </div>
                            </div>
                            <ion-icon name="open-outline" style="font-size:1.8rem;color:#bbb;"></ion-icon>
                        </a>
                        @else
                        <p style="color:var(--text-mut);font-size:1.3rem;">No additional resources provided for this course yet.</p>
                        @endif
                    </section>
                </div>

                <!-- COMMENTS TAB -->
                <div id="tab-comments" class="tab-pane">
                    @livewire('course-comments', ['courseSlug' => $slug])
                </div>
            </div>
        </div>

        <!-- Sidebar / Curriculum -->
        <div class="learn-side-col">
            <div class="section-label">Course Content</div>
            
            @php
                $totalProgress = 0;
                foreach($lessons as $lesson) {
                    $totalProgress += $lesson['progress'] ?? 0;
                }
                $overallProgress = count($lessons) > 0 ? round($totalProgress / count($lessons)) : 0;
            @endphp

            <div class="progress-widget">
                <div class="radial-lg" style="--p: {{ $overallProgress }}%;">
                    <div class="inner-circle">{{ $overallProgress }}%</div>
                </div>
                <div class="prog-info">
                    <h4>Study Progress</h4>
                    <p>Track your learning milestones and where you left off.</p>
                </div>
            </div>

            <div class="curriculum-list">
                @foreach($lessons as $index => $lesson)
                <a href="?v={{ $lesson['video_id'] }}" style="text-decoration: none; color: inherit; display: block;">
                    <div class="curr-item {{ $lesson['active'] ? 'active' : '' }}">
                        <div class="curr-left">
                            <div class="curr-num">{{ $index + 1 }}</div>
                            <div class="curr-info">
                                <h5>{{ $lesson['title'] }}</h5>
                                <span>{{ $lesson['time'] }}</span>
                            </div>
                        </div>
                        <div class="radial-sm" style="--p: {{ $lesson['progress'] }}%;"></div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- AI Chat Offcanvas Component -->
    @livewire('course-ai-chat', ['course' => $course])

@push('scripts')
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        // Tab system logic
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active from all tabs and panes
                document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                
                // Add active to clicked tab
                tab.classList.add('active');
                
                // Show matching pane
                const target = tab.getAttribute('data-tab');
                const pane = document.getElementById('tab-' + target);
                if(pane) pane.classList.add('active');
            });
        });

        function shareCourse() {
            const shareData = {
                title: '{{ addslashes($course["title"]) }}',
                text: 'Check out this amazing course on SkillUp!',
                url: window.location.href
            };
            if (navigator.share) {
                navigator.share(shareData).catch(console.error);
            } else {
                navigator.clipboard.writeText(shareData.url).then(() => {
                    alert('Course link copied to clipboard!');
                });
            }
        }
    </script>
    
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        var player;
        var progressInterval;

        function onYouTubeIframeAPIReady() {
            player = new YT.Player('player', {
                videoId: '{{ $course["video_id"] }}',
                playerVars: {
                    'rel': 0,
                    'playsinline': 1
                },
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerStateChange(event) {
            // If playing
            if (event.data == YT.PlayerState.PLAYING) {
                if(!progressInterval) {
                    progressInterval = setInterval(updateProgress, 5000); // Poll every 5s
                }
            } else {
                if(progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;
                }
                if(event.data == YT.PlayerState.PAUSED || event.data == YT.PlayerState.ENDED) {
                    updateProgress(); // final ping on pause/end
                }
            }
        }

        function updateProgress() {
            if(!player || !player.getCurrentTime) return;

            let currentTime = player.getCurrentTime();
            let duration = player.getDuration();
            if(duration <= 0) return;

            fetch('{{ route("paths.progress") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    course_slug: '{{ $slug }}',
                    video_id: '{{ $course["video_id"] }}',
                    progress_seconds: currentTime,
                    total_seconds: duration
                })
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                    console.log('Progress updated', data);
                }
            })
            .catch(err => console.error(err));
        }
    </script>
@endpush
@endsection
