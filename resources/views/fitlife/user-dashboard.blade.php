@extends('layouts.dashboard')

@section('title', 'User Dashboard')

@push('styles')
    <style>
        /* Content Area grid */
        .content-area { padding: 30px 40px; overflow-y: auto; flex-grow: 1; display: grid; grid-template-columns: 1fr 340px; gap: 30px; align-items: start; }
        
        /* Hero Banner */
        .hero-banner { background: linear-gradient(135deg, var(--brand-primary), #ff8058); border-radius: 16px; padding: 40px; color: #fff; position: relative; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(255, 69, 0, 0.2); }
        .hero-label { font-size: 1.2rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px; opacity: 0.9; }
        .hero-title { font-size: 3rem; font-weight: 800; line-height: 1.2; margin-bottom: 24px; max-width: 80%; }
        .hero-btn { background: #1c1c1c; color: #fff; border: none; padding: 12px 24px; border-radius: 30px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; }
        .hero-btn:hover { background: #000; }
        .hero-stars { position: absolute; right: 0; top: 0; width: 40%; height: 100%; opacity: 0.2; pointer-events: none; background: radial-gradient(circle, #fff 2px, transparent 2px); background-size: 30px 30px; }
        
        /* Metric Row */
        .metric-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .metric-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 16px; }
        .m-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; flex-shrink: 0; }
        .m-icon-1 { background: #f0ebff; color: #8e54e9; }
        .m-icon-2 { background: #ffe4ef; color: #ff4aa0; }
        .m-icon-3 { background: #dff6ff; color: #3aa8f2; }
        .m-details h4 { font-size: 1.5rem; color: var(--text-main); font-weight: 700; }
        .m-details p { font-size: 1.2rem; color: var(--text-mut); }
        
        /* Section Title */
        .section-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .section-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); }
        .section-nav { display: flex; gap: 8px; }
        .s-nav-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: var(--bg-surface); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main); }
        .s-nav-btn.active { background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }
        
        /* Continue Watching */
        .cw-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .cw-card { background: var(--bg-surface); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
        .cw-img { width: 100%; height: 140px; background-color: #ddd; background-size: cover; background-position: center; position: relative; }
        .cw-like { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; }
        .cw-body { padding: 16px; }
        .cw-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 1rem; font-weight: 700; margin-bottom: 10px; }
        .cw-badge-fe { background: #dff6ff; color: #3aa8f2; }
        .cw-badge-ux { background: #f0ebff; color: #8e54e9; }
        .cw-badge-br { background: #ffe4ef; color: #ff4aa0; }
        .cw-title { font-size: 1.4rem; font-weight: 700; line-height: 1.4; margin-bottom: 14px; min-height: 40px; }
        .cw-mentor { display: flex; align-items: center; gap: 8px; border-top: 1px dashed var(--border-color); padding-top: 12px; }
        .cw-mentor-ava { width: 28px; height: 28px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; }
        .cw-mentor-name { font-size: 1.2rem; color: var(--text-mut); font-weight: 700; }
        
        /* Table */
        .lesson-table { width: 100%; border-collapse: collapse; background: var(--bg-surface); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
        .lesson-table th { background: #fdfdfd; padding: 14px 20px; text-align: left; font-size: 1.1rem; color: var(--text-mut); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); }
        .lesson-table td { padding: 16px 20px; font-size: 1.3rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .lesson-table tr:last-child td { border-bottom: none; }
        .tbl-mentor { display: flex; align-items: center; gap: 12px; }
        .tbl-m-info h5 { font-size: 1.3rem; font-weight: 700; color: var(--text-main); }
        .tbl-m-info p { font-size: 1.1rem; color: var(--text-mut); }
        .tbl-badge { background: #f6f7f8; color: var(--text-main); padding: 6px 12px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .action-arrow { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--text-mut); text-decoration: none; transition: 0.2s; }
        .action-arrow:hover { border-color: var(--brand-primary); color: var(--brand-primary); }

        /* Recent Chats Widget */
        .chat-widget { background: var(--bg-surface); border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 30px; }
        .chat-widget-list { display: flex; flex-direction: column; }
        .cw-item { display: flex; padding: 12px 20px; gap: 12px; border-bottom: 1px solid #f2f2f2; text-decoration: none; color: inherit; align-items: center; }
        .cw-item:last-child { border-bottom: none; }
        .cw-item:hover { background: #fafafa; }
        .cw-avatar { width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0; overflow: hidden; border: 1px solid #eee; }
        .cw-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .cw-info { flex-grow: 1; min-width: 0; }
        .cw-name { font-size: 1.3rem; font-weight: 600; color: #000; margin-bottom: 2px; }
        .cw-text { font-size: 1.2rem; color: var(--text-mut); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cw-unread { width: 8px; height: 8px; border-radius: 50%; background: var(--brand-primary); }

        /* Right Panel */
        .right-panel { display: flex; flex-direction: column; gap: 30px; }
        
        /* Stats Widget */
        .stat-widget { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; text-align: center; }
        .stat-radial { width: 140px; height: 140px; margin: 0 auto 20px; position: relative; border-radius: 50%; background: conic-gradient(var(--brand-primary) 32%, #f0f0f0 0); display: flex; align-items: center; justify-content: center; }
        .stat-inner { width: 120px; height: 120px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;}
        .stat-val { position: absolute; top: 0; right: 0; background: var(--brand-primary); color: #fff; padding: 4px 8px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; border: 2px solid #fff; }
        .stat-greeting { font-size: 1.8rem; font-weight: 800; margin-bottom: 6px; }
        .stat-sub { font-size: 1.2rem; color: var(--text-mut); margin-bottom: 24px; }
        
        /* Bar Chart Mock */
        .bar-chart { display: flex; align-items: flex-end; justify-content: space-between; height: 80px; margin-bottom: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; }
        .bar { width: 30%; background: #e0e9f8; border-radius: 4px; transition: 0.3s; }
        .bar.active { background: var(--brand-primary); height: 100% !important; }
        .bar-labels { display: flex; justify-content: space-between; font-size: 1.1rem; color: var(--text-mut); }

        /* Mentor Widget */
        .mentor-widget { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; }
        .mentor-list { display: flex; flex-direction: column; gap: 16px; margin-top: 16px; }
        .mentor-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 16px; }
        .mentor-item:last-child { border-bottom: none; padding-bottom: 0; }
        .m-follow-btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border: 1px solid var(--brand-primary); color: var(--brand-primary); border-radius: 20px; font-size: 1.2rem; font-weight: 700; background: none; cursor: pointer; transition: 0.2s; }
        .m-follow-btn:hover { background: var(--brand-primary); color: #fff; }
        .btn-full { width: 100%; display: block; padding: 12px; background: rgba(255, 69, 0, 0.08); color: var(--brand-primary); text-align: center; border-radius: 8px; font-weight: 700; font-size: 1.4rem; text-decoration: none; margin-top: 20px; transition: 0.2s;}
        .btn-full:hover { background: var(--brand-primary); color: #fff; }

        @media (max-width: 1200px) {
            .content-area { grid-template-columns: 1fr; }
            .right-panel { flex-direction: row; }
            .right-panel > div { flex: 1; }
        }
        @media (max-width: 992px) {
            .cw-grid, .metric-row { grid-template-columns: repeat(2, 1fr); }
            .hero-title { font-size: 2.2rem; }
            .content-area { padding: 20px; }
            .right-panel { flex-direction: column; width: 100%; }
        }
        @media (max-width: 768px) {
            .cw-grid, .metric-row { grid-template-columns: 1fr; }
            .hero-banner { padding: 30px 24px; }
            .hero-title { font-size: 1.8rem; }
            .lesson-table thead { display: none; }
            .lesson-table, .lesson-table tbody, .lesson-table tr, .lesson-table td { display: block; width: 100%; }
            .lesson-table tr { margin-bottom: 16px; border-bottom: 1px solid var(--border-color); }
            .lesson-table td { border-bottom: none; display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; text-align: right; }
            .lesson-table td::before { content: attr(data-label); font-weight: 700; color: var(--text-mut); text-transform: uppercase; font-size: 1rem; float: left; }
        }
    </style>
@endpush

@section('content')
    <div class="left-panel">
        <div class="hero-banner">
            <div class="hero-stars"></div>
            <div class="hero-label">Online Course</div>
            <h1 class="hero-title">Sharpen Your Skills with Professional Online Courses</h1>
            <a href="{{ route('courses') }}" class="hero-btn">Join Now <ion-icon name="chevron-forward-outline"></ion-icon></a>
        </div>

        <div class="metric-row">
            <div class="metric-card">
                <div class="m-icon m-icon-1"><ion-icon name="cube-outline"></ion-icon></div>
                <div class="m-details">
                    <p>2/8 watched</p>
                    <h4>UI/UX Design</h4>
                </div>
            </div>
            <div class="metric-card">
                <div class="m-icon m-icon-2"><ion-icon name="color-palette-outline"></ion-icon></div>
                <div class="m-details">
                    <p>3/8 watched</p>
                    <h4>Branding</h4>
                </div>
            </div>
            <div class="metric-card">
                <div class="m-icon m-icon-3"><ion-icon name="desktop-outline"></ion-icon></div>
                <div class="m-details">
                    <p>6/12 watched</p>
                    <h4>Front End</h4>
                </div>
            </div>
        </div>

        <div class="section-hdr">
            <h2 class="section-title">Continue Watching</h2>
            <div class="section-nav">
                <button class="s-nav-btn"><ion-icon name="chevron-back-outline"></ion-icon></button>
                <button class="s-nav-btn active"><ion-icon name="chevron-forward-outline"></ion-icon></button>
            </div>
        </div>
        
        <div class="cw-grid">
            @foreach($continueWatching as $index => $cw)
            <div class="cw-card">
                <div class="cw-img" style="background-image: url('{{ $cw['image'] }}');">
                    <div class="cw-like"><ion-icon name="heart-outline"></ion-icon></div>
                </div>
                <div class="cw-body">
                    @php
                        $badgeClass = match($cw['category']) {
                            'UI/UX DESIGN' => 'cw-badge-ux',
                            'BRANDING' => 'cw-badge-br',
                            default => 'cw-badge-fe'
                        };
                    @endphp
                    <span class="cw-badge {{ $badgeClass }}">{{ \Str::title(strtolower($cw['category'])) }}</span>
                    <h3 class="cw-title">{{ \Str::limit($cw['title'], 48) }}</h3>
                    <div class="cw-mentor">
                        <div class="cw-mentor-ava">{{ strtoupper(substr($cw['mentor'], 0, 1)) }}</div>
                        <span class="cw-mentor-name">{{ $cw['mentor'] }}<br><span style="font-weight:400;">Mentor</span></span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="section-hdr" style="margin-top: 40px;">
            <h2 class="section-title">Recent Chats</h2>
            <a href="{{ route('dashboard.chats') }}" style="color:var(--brand-primary); font-weight:700; font-size:1.3rem; text-decoration:none;">Open App</a>
        </div>

        <div class="chat-widget">
            <div class="chat-widget-list">
                @foreach($conversations as $chat)
                <a href="{{ route('dashboard.chats') }}" class="cw-item">
                    <div class="cw-avatar">
                        <img src="{{ $chat['avatar'] }}" alt="{{ $chat['name'] }}">
                    </div>
                    <div class="cw-info">
                        <div class="cw-name">{{ $chat['name'] }}</div>
                        <div class="cw-text">{{ $chat['message'] }}</div>
                    </div>
                    <div style="font-size: 1.1rem; color: var(--text-mut); margin-left: 10px;">{{ $chat['time'] }}</div>
                    @if($chat['unread'])
                    <div class="cw-unread" style="margin-left: 10px;"></div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>

        <div class="section-hdr" style="margin-top: 40px;">
            <h2 class="section-title">Your Lesson</h2>
            <a href="#" style="color:var(--brand-primary); font-weight:700; font-size:1.3rem; text-decoration:none;">See all</a>
        </div>

        <table class="lesson-table">
            <thead>
                <tr>
                    <th>Mentor</th>
                    <th>Type</th>
                    <th>Desc</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lessons as $lesson)
                <tr>
                    <td data-label="Mentor">
                        <div class="tbl-mentor">
                            <div class="cw-mentor-ava" style="width:36px; height:36px; font-size:1.2rem;">{{ strtoupper(substr($lesson['mentor'], 0, 1)) }}</div>
                            <div class="tbl-m-info">
                                <h5>{{ $lesson['mentor'] }}</h5>
                                <p>{{ $lesson['date'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td data-label="Type">
                        <span class="tbl-badge"><ion-icon name="color-filter-outline"></ion-icon> {{ \Str::title(strtolower($lesson['type'])) }}</span>
                    </td>
                    <td data-label="Description" style="font-weight: 500; color: var(--text-main);">{{ $lesson['desc'] }}</td>
                    <td data-label="Action">
                        <a href="#" class="action-arrow"><ion-icon name="arrow-forward-outline"></ion-icon></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Right Column -->
    <div class="right-panel">
        <div class="stat-widget">
            <div class="section-hdr" style="margin-bottom: 24px;">
                <h2 class="section-title">Statistic</h2>
                <ion-icon name="ellipsis-vertical" style="color:var(--text-mut); font-size:1.8rem;"></ion-icon>
            </div>
            
            <div class="stat-radial">
                <div class="stat-inner">
                    <img src="https://ui-avatars.com/api/?name={{ rawurlencode(auth()->user()->name) }}&background=f0ebff&color=8e54e9&rounded=true&size=100" alt="Avatar" style="width: 100%; height: 100%;">
                </div>
                <div class="stat-val">32%</div>
            </div>
            
            <div class="stat-greeting">Good Morning {{ explode(' ', auth()->user()->name)[0] }} 🔥</div>
            <div class="stat-sub">Continue your learning to achieve your target!</div>
            
            <div class="bar-chart">
                <div class="bar" style="height: 30%;"></div>
                <div class="bar" style="height: 50%;"></div>
                <div class="bar" style="height: 20%;"></div>
                <div class="bar active" style="height: 100%;"></div>
                <div class="bar" style="height: 40%;"></div>
            </div>
            <div class="bar-labels">
                <span>1-10 Aug</span>
                <span>11-20 Aug</span>
                <span>21-30 Aug</span>
            </div>
        </div>

        <div class="mentor-widget">
            <div class="section-hdr">
                <h2 class="section-title">Your mentor</h2>
                <button class="s-nav-btn" style="border:none; color:var(--text-mut);"><ion-icon name="add-outline"></ion-icon></button>
            </div>
            
            <div class="mentor-list">
                @foreach($mentors as $mentor)
                <div class="mentor-item">
                    <div class="tbl-mentor">
                        <div class="cw-mentor-ava" style="width:40px; height:40px; font-size:1.4rem;">{{ $mentor['avatar'] }}</div>
                        <div class="tbl-m-info">
                            <h5>{{ $mentor['name'] }}</h5>
                            <p>{{ $mentor['role'] }}</p>
                        </div>
                    </div>
                    <button class="m-follow-btn"><ion-icon name="person-add-outline"></ion-icon> Follow</button>
                </div>
                @endforeach
            </div>
            
            <a href="#" class="btn-full">See All</a>
        </div>
    </div>
@endsection
