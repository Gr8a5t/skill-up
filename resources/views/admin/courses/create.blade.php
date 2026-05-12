@extends('layouts.admin')

@section('title', 'Admin - Create Course')

@push('styles')
    <style>
        .page-header { margin-bottom: 30px; }
        .page-title { font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .page-sub { color: var(--text-mut); font-size: 1.3rem; font-weight: 500; }

        .form-container { display: grid; grid-template-columns: 1fr 300px; gap: 25px; align-items: start; }
        .form-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 20px; padding: 25px; }
        .form-section { border-bottom: 1px solid var(--border-color); padding-bottom: 25px; margin-bottom: 25px; }
        .form-section:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .section-title { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-title ion-icon { color: var(--brand-primary); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group label { font-size: 1.15rem; font-weight: 700; color: var(--text-mut); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { background: var(--bg-deep); border: 1px solid var(--border-color); border-radius: 12px; padding: 12px 15px; color: #fff; font-family: inherit; font-size: 1.35rem; outline: none; transition: 0.3s; width: 100%; }
        .form-control:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 4px rgba(255, 69, 0, 0.1); }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 15px center; background-size: 16px; padding-right: 40px; cursor: pointer; }
        select.form-control option { background: #0a0a0c; color: #fff; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .field-hint { font-size: 1.1rem; color: var(--text-mut); margin-top: 4px; }

        /* Lessons */
        .lesson-list { display: flex; flex-direction: column; gap: 12px; }
        .lesson-item { background: var(--bg-deep); border: 1px solid var(--border-color); border-radius: 12px; padding: 15px; display: grid; grid-template-columns: 36px 1fr 1fr 36px; gap: 12px; align-items: center; }
        .lesson-num { width: 28px; height: 28px; border-radius: 8px; background: rgba(255,69,0,0.1); display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--brand-primary); font-size: 1.2rem; }
        .btn-remove-lesson { color: #ef4444; font-size: 2rem; cursor: pointer; transition: 0.2s; line-height: 1; }
        .btn-remove-lesson:hover { transform: scale(1.15); }
        .btn-add-lesson { width: 100%; padding: 12px; border-radius: 12px; border: 2px dashed var(--border-color); color: var(--text-mut); font-weight: 700; font-size: 1.3rem; margin-top: 12px; background: none; transition: 0.3s; cursor: pointer; }
        .btn-add-lesson:hover { border-color: var(--brand-primary); color: var(--brand-primary); background: rgba(255,69,0,0.05); }

        /* Sidebar */
        .action-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 20px; padding: 20px; position: sticky; top: 30px; display: flex; flex-direction: column; gap: 12px; }
        .action-label { font-size: 1.1rem; color: var(--text-mut); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 10px; }
        .btn-publish { width: 100%; background: var(--brand-primary); color: #fff; padding: 14px; border-radius: 12px; font-weight: 800; font-size: 1.4rem; border: none; cursor: pointer; transition: 0.3s; }
        .btn-publish:hover { background: var(--brand-secondary); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255,69,0,0.25); }
        .btn-draft { width: 100%; background: var(--bg-deep); color: var(--text-mut); padding: 12px; border-radius: 12px; font-weight: 700; font-size: 1.3rem; border: 1px solid var(--border-color); cursor: pointer; transition: 0.3s; }
        .btn-draft:hover { color: #fff; border-color: #fff; }
        .btn-back { color: var(--text-mut); font-size: 1.2rem; text-align: center; text-decoration: none; display: block; padding: 8px; transition: 0.2s; }
        .btn-back:hover { color: #fff; }

        .toggle-row { display: flex; align-items: center; justify-content: space-between; }
        .toggle-label { font-size: 1.3rem; font-weight: 600; color: #fff; }
        .toggle input[type="checkbox"] { width: 36px; height: 20px; appearance: none; background: var(--border-color); border-radius: 30px; position: relative; cursor: pointer; transition: 0.3s; }
        .toggle input[type="checkbox"]:checked { background: var(--brand-primary); }
        .toggle input[type="checkbox"]::after { content: ''; position: absolute; left: 3px; top: 3px; width: 14px; height: 14px; background: #fff; border-radius: 50%; transition: 0.3s; }
        .toggle input[type="checkbox"]:checked::after { transform: translateX(16px); }

        /* Errors */
        .error-box { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; padding: 15px; margin-bottom: 20px; }
        .error-box ul { list-style: none; display: flex; flex-direction: column; gap: 6px; }
        .error-box li { font-size: 1.2rem; color: #ef4444; display: flex; align-items: center; gap: 8px; }

        @media (max-width: 1100px) { .form-container { grid-template-columns: 1fr; } .action-card { position: static; } }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } .lesson-item { grid-template-columns: 36px 1fr 36px; } .lesson-item input:last-of-type { grid-column: 2; } }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Create New Course</h1>
        <p class="page-sub">Fill in the details to publish a new learning course.</p>
    </div>

    @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $error)
                    <li><ion-icon name="alert-circle-outline"></ion-icon> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.courses.store') }}" method="POST" class="form-container">
        @csrf

        <div class="form-card">
            {{-- COURSE DETAILS --}}
            <div class="form-section">
                <h3 class="section-title"><ion-icon name="information-circle-outline"></ion-icon> Course Details</h3>
                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>Course Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Modern JavaScript Bootcamp" required>
                    </div>
                    <div class="input-group">
                        <label>Category *</label>
                        <select name="category" class="form-control">
                            @foreach(['Frontend','Backend','Mobile Dev','AI & Data','Design','DevOps'] as $cat)
                                <option {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Difficulty Level *</label>
                        <select name="level" class="form-control">
                            @foreach(['Beginner','Medium','Advance'] as $lvl)
                                <option {{ old('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Icon Name (IonIcon)</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', 'book-outline') }}" placeholder="e.g. logo-javascript">
                        <p class="field-hint">Browse icons at <a href="https://ionic.io/ionicons" target="_blank" style="color:var(--brand-primary)">ionicons.io</a></p>
                    </div>
                    <div class="input-group">
                        <label>Card Background Color</label>
                        <input type="color" name="color" class="form-control" value="{{ old('color', '#f5f5f5') }}" style="height:48px; padding: 4px 8px; cursor:pointer;">
                    </div>
                    <div class="input-group full-width">
                        <label>YouTube Playlist ID</label>
                        <input type="text" name="playlist_id" class="form-control" value="{{ old('playlist_id') }}" placeholder="e.g. PL4cUxeGkcC9haQlqdCQyYmL_27TesCGPC">
                    </div>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="form-section">
                <h3 class="section-title"><ion-icon name="document-text-outline"></ion-icon> Summary & Concepts</h3>
                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>Lesson Recap / Description</label>
                        <textarea name="recap" class="form-control" placeholder="A detailed recap of what this course covers...">{{ old('recap') }}</textarea>
                    </div>
                    <div class="input-group full-width">
                        <label>Key Concepts <span style="text-transform:none; opacity:0.6; font-size:0.9em;">– one per line</span></label>
                        <textarea name="key_concepts" class="form-control" style="min-height: 90px;" placeholder="Understanding fundamentals&#10;State management&#10;Production deployment">{{ old('key_concepts') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- LESSONS --}}
            <div class="form-section">
                <h3 class="section-title"><ion-icon name="play-circle-outline"></ion-icon> Lessons Curriculum</h3>
                <div class="lesson-list" id="lessonList">
                    <div class="lesson-item" data-index="0">
                        <div class="lesson-num">1</div>
                        <input type="text" name="lessons[0][title]" class="form-control" placeholder="Lesson Title" required>
                        <input type="text" name="lessons[0][video_id]" class="form-control" placeholder="YouTube Video ID" required>
                        <ion-icon name="close-circle-outline" class="btn-remove-lesson" onclick="removeLesson(this)"></ion-icon>
                    </div>
                </div>
                <button type="button" class="btn-add-lesson" onclick="addLesson()">
                    <ion-icon name="add-circle-outline"></ion-icon> Add Another Lesson
                </button>
            </div>

            {{-- ASSETS & RESOURCES --}}
            <div class="form-section">
                <h3 class="section-title">
                    <ion-icon name="folder-open-outline"></ion-icon> Assets & Resources
                    <span style="font-size:1rem; color:var(--text-mut); font-weight:500; margin-left:8px;">(Optional)</span>
                </h3>
                <div class="form-grid">
                    <div class="input-group">
                        <label>Source Files URL <span style="text-transform:none; opacity:0.6; font-size:0.85em;">– optional</span></label>
                        <input type="url" name="source_files_url" class="form-control" value="{{ old('source_files_url') }}" placeholder="https://github.com/...">
                    </div>
                    <div class="input-group">
                        <label>Cheat Sheet / Extra URL <span style="text-transform:none; opacity:0.6; font-size:0.85em;">– optional</span></label>
                        <input type="url" name="cheatsheet_url" class="form-control" value="{{ old('cheatsheet_url') }}" placeholder="https://docs.example.com/...">
                    </div>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="action-card">
            <p class="action-label">Publish Settings</p>
            <div class="toggle-row">
                <span class="toggle-label">Publish Immediately</span>
                <label class="toggle">
                    <input type="checkbox" name="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
                </label>
            </div>
            <button type="submit" class="btn-publish">
                <ion-icon name="checkmark-circle-outline"></ion-icon> Save Course
            </button>
            <a href="{{ route('admin.courses') }}" class="btn-back">← Cancel</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let lessonCount = 1;

    function addLesson() {
        const list = document.getElementById('lessonList');
        const idx = lessonCount++;
        const div = document.createElement('div');
        div.className = 'lesson-item';
        div.dataset.index = idx;
        div.innerHTML = `
            <div class="lesson-num">${idx + 1}</div>
            <input type="text" name="lessons[${idx}][title]" class="form-control" placeholder="Lesson Title" required>
            <input type="text" name="lessons[${idx}][video_id]" class="form-control" placeholder="YouTube Video ID" required>
            <ion-icon name="close-circle-outline" class="btn-remove-lesson" onclick="removeLesson(this)"></ion-icon>
        `;
        list.appendChild(div);
        renumberLessons();
    }

    function removeLesson(el) {
        const items = document.querySelectorAll('.lesson-item');
        if (items.length <= 1) return; // keep at least one
        el.closest('.lesson-item').remove();
        renumberLessons();
    }

    function renumberLessons() {
        document.querySelectorAll('.lesson-item').forEach((item, i) => {
            item.querySelector('.lesson-num').textContent = i + 1;
            item.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, `[${i}]`);
            });
        });
    }
</script>
@endpush
