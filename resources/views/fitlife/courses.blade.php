@extends('layouts.dashboard')

@section('title', 'All Courses')

@push('styles')
    <style>
        .content-area { padding: 40px; background: #fbfbfb; }
        
        .page-header { margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; }
        .page-title { font-size: 1.8rem; font-weight: 700; color: #1c1c1c; display: flex; align-items: center; gap: 10px; }
        .count-badge { background: #eee; padding: 2px 8px; border-radius: 6px; font-size: 1.2rem; color: #666; font-weight: 500; }
        
        .header-controls { display: flex; align-items: center; gap: 10px; }
        .ctrl-btn { display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #fff; border: 1px solid #eee; border-radius: 8px; font-size: 1.2rem; font-weight: 600; color: #555; cursor: pointer; transition: 0.2s; }
        .ctrl-btn:hover { background: #f9f9f9; border-color: #ddd; }
        .view-toggle { display: flex; background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; }
        .toggle-btn { padding: 6px 10px; cursor: pointer; color: #555; font-size: 1.6rem; display: flex; align-items: center; justify-content: center; }
        .toggle-btn.active { background: #1c1c1c; color: #fff; }

        .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; }
        .course-card { background: #fff; border-radius: 14px; border: 1px solid #efefef; overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .course-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        
        .card-hdr { height: 150px; position: relative; display: flex; align-items: center; justify-content: center; }
        .lesson-badge { position: absolute; top: 14px; left: 14px; background: rgba(0,0,0,0.05); padding: 5px 10px; border-radius: 6px; font-size: 1.1rem; font-weight: 700; color: #444; }
        .card-icon { font-size: 5.5rem; color: rgba(0,0,0,0.7); }
        
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
        .tag-badge { background: #fff; color: #888; padding: 4px 10px; border-radius: 6px; font-size: 1.05rem; font-weight: 600; border: 1px solid #efefef; }
        .card-title { font-size: 1.55rem; font-weight: 700; color: #1c1c1c; line-height: 1.35; margin-bottom: 20px; min-height: 42px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        .card-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 16px; border-top: 1px solid #f8f8f8; }
        .footer-left { font-size: 1.25rem; color: #999; font-weight: 500; display: flex; align-items: center; gap: 4px; }
        .footer-left span { color: #1c1c1c; font-weight: 700; }
        
        .prog-wrap { display: flex; align-items: center; gap: 10px; font-size: 1.25rem; font-weight: 700; color: #1c1c1c; }
        .prog-circle { 
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            background: conic-gradient(var(--brand-primary) calc(var(--p) * 1%), #eee 0); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            position: relative; 
        }
        .prog-circle::after { content: ''; position: absolute; width: 15px; height: 15px; background: #fff; border-radius: 50%; }
        
        .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        
        @media (max-width: 1200px) {
            .course-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 992px) {
            .content-area { padding: 20px; }
            .course-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .course-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .content-area { padding: 20px; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 20px; }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            All Courses <span class="count-badge">28</span>
        </h1>
        <div class="header-controls">
            <button class="ctrl-btn"><ion-icon name="options-outline"></ion-icon> Filter</button>
            <button class="ctrl-btn"><ion-icon name="swap-vertical-outline"></ion-icon> Sort by</button>
            <div class="view-toggle">
                <div class="toggle-btn"><ion-icon name="menu-outline"></ion-icon></div>
                <div class="toggle-btn active"><ion-icon name="grid-outline"></ion-icon></div>
            </div>
        </div>
    </div>

    <div class="course-grid">
        @foreach($courses as $course)
        <a href="{{ route('courses.learn', $course['slug']) }}" class="course-card" style="text-decoration: none;">
            <div class="card-hdr" style="background-color: {{ $course['color'] }};">
                <ion-icon name="{{ $course['icon'] }}" class="card-icon"></ion-icon>
            </div>
            <div class="card-body">
                <div class="card-tags">
                    @foreach($course['tags'] as $tag)
                    <span class="tag-badge">{{ $tag }}</span>
                    @endforeach
                </div>
                <h3 class="card-title">{{ $course['title'] }}</h3>
                
                <div class="card-footer">
                    <div class="footer-left">Level: <span>{{ $course['level'] }}</span></div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
@endsection
