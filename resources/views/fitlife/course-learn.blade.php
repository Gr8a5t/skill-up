@extends('layouts.dashboard')

@section('title', $course['title'])

@push('styles')
    <style>
        :root {
            --brand-primary: #ff4500 !important;
            --border-color: #edeff1 !important;
        }
        /* Force layout overrides */
        /* Force layout overrides */
        body { overflow: hidden !important; }
        body .layout-wrapper { padding-left: 260px !important; margin: 0 !important; width: 100% !important; max-width: none !important; }
        
        @media (max-width: 992px) { 
            body .layout-wrapper { padding-left: 0 !important; } 
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

        .tabs { display: flex; gap: 24px; border-bottom: 1px solid #f2f2f2; margin-bottom: 24px; }
        .tab-item { padding-bottom: 10px; font-size: 1.3rem; font-weight: 700; color: #888; cursor: pointer; position: relative; display: flex; align-items: center; gap: 6px; }
        .tab-item.active { color: var(--brand-primary); }
        .tab-item.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 2px; background: var(--brand-primary); }

        .content-section { margin-bottom: 30px; }
        .section-label { font-size: 1.6rem; font-weight: 800; color: #1c1c1c; margin-bottom: 12px; }
        .section-text { font-size: 1.4rem; line-height: 1.6; color: #555; }
        
        .concepts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; margin-top: 16px; }
        .concept-card { background: #fbfbfb; border: 1px solid #f0f0f0; border-radius: 10px; padding: 14px; display: flex; align-items: center; gap: 10px; }
        .concept-card ion-icon { font-size: 1.6rem; color: #23a55a; flex-shrink: 0; }
        .concept-card p { font-size: 1.25rem; font-weight: 600; color: #444; line-height: 1.3; }

        /* Learn Sidebar Column */
        .learn-side-col { width: 300px; flex-shrink: 0; padding: 25px; background: #fafafa; overflow-y: auto; height: 100%; }
        
        .progress-widget { background: #fff; border: 1px solid var(--border-color); border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .radial-lg { 
            width: 50px; height: 50px; border-radius: 50%; 
            background: conic-gradient(var(--brand-primary) 75%, #eee 0); 
            display: flex; align-items: center; justify-content: center; position: relative;
        }
        .radial-lg::after { content: '75%'; position: absolute; width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 800; color: #1c1c1c; }
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
            .action-row { width: 100%; justify-content: space-between; }
        }
    </style>
@endpush

@section('content')
    <div class="learn-wrapper">
        <!-- Main Panel -->
        <div class="learn-main-col">
            <nav class="breadcrumb">
                Courses <ion-icon name="chevron-forward"></ion-icon> 
                {{ $course['category'] }} <ion-icon name="chevron-forward"></ion-icon>
                <span>{{ $course['title'] }}</span>
            </nav>

            <header class="course-header">
                <h1 class="course-title">{{ $course['title'] }}</h1>
                <div class="action-row">
                    <button class="icon-btn"><ion-icon name="bookmark-outline"></ion-icon></button>
                    <button class="share-btn"><ion-icon name="share-social-outline"></ion-icon> Share</button>
                    <img src="https://i.pravatar.cc/150?u=instructor" style="width: 44px; height: 44px; border-radius: 10px; margin-left: 10px;" alt="Instructor">
                </div>
            </header>

            <div class="meta-row">
                <div class="meta-item"><ion-icon name="ribbon-outline"></ion-icon> {{ $course['level'] }}</div>
                <div class="meta-item"><ion-icon name="list-outline"></ion-icon> {{ $course['lessons_count'] }} Lessons</div>
                <div class="meta-item"><ion-icon name="time-outline"></ion-icon> {{ $course['duration'] }}</div>
            </div>

            <div class="video-box">
                <iframe src="https://www.youtube.com/embed/{{ $course['video_id'] }}?rel=0" allowfullscreen></iframe>
            </div>

            <div class="tabs">
                <div class="tab-item active"><ion-icon name="document-text-outline"></ion-icon> Summary</div>
                <div class="tab-item"><ion-icon name="folder-open-outline"></ion-icon> Files</div>
                <div class="tab-item"><ion-icon name="link-outline"></ion-icon> Resources</div>
                <div class="tab-item"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Q&A</div>
            </div>

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

        <!-- Sidebar / Curriculum -->
        <div class="learn-side-col">
            <div class="section-label">Course Content</div>
            
            <div class="progress-widget">
                <div class="radial-lg"></div>
                <div class="prog-info">
                    <h4>Study Progress</h4>
                    <p>Track your learning milestones and where you left off.</p>
                </div>
            </div>

            <div class="curriculum-list">
                @foreach($lessons as $index => $lesson)
                <div class="curr-item {{ isset($lesson['active']) ? 'active' : '' }}">
                    <div class="curr-left">
                        <div class="curr-num">{{ $index + 1 }}</div>
                        <div class="curr-info">
                            <h5>{{ $lesson['title'] }}</h5>
                            <span>{{ $lesson['time'] }}</span>
                        </div>
                    </div>
                    <div class="radial-sm" style="--p: {{ $lesson['progress'] }}%;"></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@push('scripts')
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
@endpush
@endsection
