@extends('layouts.admin')

@section('title', 'Admin - Courses')

@push('styles')
    <style>
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .page-title { font-size: 2.2rem; font-weight: 800; color: #fff; }
        .btn-add { background: var(--brand-primary); color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-size: 1.3rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-add:hover { background: var(--brand-secondary); transform: translateY(-2px); }

        .alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); border-radius: 12px; padding: 14px 18px; color: #22c55e; font-size: 1.3rem; font-weight: 600; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }

        .empty-state { text-align: center; padding: 80px 20px; color: var(--text-mut); }
        .empty-state ion-icon { font-size: 5rem; color: var(--border-color); margin-bottom: 15px; display: block; }
        .empty-state h2 { font-size: 2rem; color: #fff; margin-bottom: 8px; }
        .empty-state p { font-size: 1.4rem; margin-bottom: 25px; }

        .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .course-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; }
        .course-card:hover { transform: translateY(-4px); border-color: rgba(255,69,0,0.4); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card-img { height: 150px; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: rgba(0,0,0,0.6); position: relative; }
        .level-tag { position: absolute; top: 12px; right: 12px; padding: 4px 8px; border-radius: 6px; font-size: 1rem; font-weight: 800; text-transform: uppercase; }
        .pub-tag { position: absolute; top: 12px; left: 12px; padding: 4px 8px; border-radius: 6px; font-size: 1rem; font-weight: 700; }
        .pub-yes { background: rgba(34,197,94,0.15); color: #22c55e; }
        .pub-no { background: rgba(148,163,184,0.15); color: var(--text-mut); }
        .lvl-beginner { background: rgba(34,197,94,0.2); color: #22c55e; }
        .lvl-medium { background: rgba(234,179,8,0.2); color: #eab308; }
        .lvl-advance { background: rgba(239,68,68,0.2); color: #ef4444; }
        .card-body { padding: 18px; flex: 1; display: flex; flex-direction: column; }
        .card-cat { color: var(--text-mut); font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .card-title { font-size: 1.45rem; font-weight: 700; color: #fff; line-height: 1.4; margin-bottom: 12px; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-meta { display: flex; align-items: center; gap: 15px; padding-top: 12px; border-top: 1px solid var(--border-color); color: var(--text-mut); font-size: 1.2rem; margin-bottom: 14px; }
        .meta-item { display: flex; align-items: center; gap: 5px; }
        .card-actions { display: flex; gap: 10px; }
        .btn-edit { flex: 1; text-align: center; padding: 9px; border-radius: 8px; border: 1px solid var(--border-color); color: #fff; text-decoration: none; font-size: 1.2rem; font-weight: 600; transition: 0.2s; }
        .btn-edit:hover { border-color: var(--brand-primary); background: rgba(255,69,0,0.08); }
        .btn-delete { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border-color); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; background: none; cursor: pointer; transition: 0.2s; }
        .btn-delete:hover { background: rgba(239,68,68,0.1); }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">All Courses <span style="font-size:1.4rem; color:var(--text-mut);">({{ $courses->count() }})</span></h1>
        <a href="{{ route('admin.courses.create') }}" class="btn-add">
            <ion-icon name="add-outline"></ion-icon> Add New Course
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
            {{ session('success') }}
        </div>
    @endif

    @if($courses->isEmpty())
        <div class="empty-state">
            <ion-icon name="book-outline"></ion-icon>
            <h2>No courses yet</h2>
            <p>Create your first course to get started.</p>
            <a href="{{ route('admin.courses.create') }}" class="btn-add">
                <ion-icon name="add-outline"></ion-icon> Create First Course
            </a>
        </div>
    @else
        <div class="course-grid">
            @foreach($courses as $course)
            <div class="course-card">
                <div class="card-img" style="background-color: {{ $course->color }};">
                    <ion-icon name="{{ $course->icon }}"></ion-icon>
                    <span class="level-tag lvl-{{ strtolower($course->level) }}">{{ $course->level }}</span>
                    <span class="pub-tag {{ $course->is_published ? 'pub-yes' : 'pub-no' }}">
                        {{ $course->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="card-cat">{{ $course->category }}</div>
                    <h3 class="card-title">{{ $course->title }}</h3>
                    <div class="card-meta">
                        <div class="meta-item"><ion-icon name="play-circle-outline"></ion-icon> {{ $course->lessons_count }} Lessons</div>
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn-edit">Edit</a>
                        <form method="POST" action="{{ route('admin.courses.delete', $course) }}" onsubmit="return confirm('Delete this course?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete"><ion-icon name="trash-outline"></ion-icon></button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@endsection
