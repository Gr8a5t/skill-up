@extends('layouts.fitlife')

@section('title', 'Learning: ' . $path['title'] . ' | SkillUp')

@section('content')
<main>
    <article class="course-detail-page">
        <section class="section course-unified" aria-label="course details">
            <div class="container container-large">
                
                <!-- Unified Breadcrumb -->
                <div class="course-unified-breadcrumb">
                    <a href="{{ route('home') }}"><ion-icon name="home-outline"></ion-icon></a>
                    <a href="{{ route('paths') }}">Learning Paths</a>
                    <span>/</span>
                    <a href="#">{{ $path['focus'] }}</a>
                    <span>/</span>
                    <span>{{ $path['title'] }}</span>
                </div>

                <!-- Unified Header -->
                <div class="course-unified-header">
                    <div class="header-left">
                        <a href="{{ route('paths') }}" class="btn-back"><ion-icon name="chevron-back-outline"></ion-icon></a>
                        <h1 class="h2">{{ $path['title'] }}</h1>
                        <span class="badge">{{ $path['focus'] }}</span>
                    </div>
                    <div class="header-right">
                        <div class="progress-container-inline">
                            <span class="progress-label">Path Progress:</span>
                            <strong id="progress-percent-header">{{ round((count($completedModules) / count($path['modules'])) * 100) }}%</strong>
                        </div>
                    </div>
                </div>

                <!-- Unified Meta -->
                <ul class="course-unified-meta">
                    <li><ion-icon name="layers-outline"></ion-icon> {{ count($path['modules']) }} modules</li>
                    <li><ion-icon name="time-outline"></ion-icon> {{ $path['duration'] }}</li>
                    <li><ion-icon name="ribbon-outline"></ion-icon> Certificate on completion</li>
                </ul>

                <div class="course-unified-grid">
                    <!-- Player Main Column -->
                    <div class="course-unified-left">
                        
                        <div class="video-stage">
                            <!-- YouTube Embed Container -->
                            <div id="player-container" class="player-ratio">
                                <div id="youtube-player"></div>
                                <div id="player-placeholder" class="player-overlay">
                                    <div class="overlay-content">
                                        <ion-icon name="play-circle-outline"></ion-icon>
                                        <p>Select a lesson from the sidebar to begin learning.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Overlay -->
                            <div id="quiz-overlay" class="quiz-overlay hidden">
                                <div class="quiz-card glass">
                                    <div class="quiz-header">
                                        <span class="quiz-badge">Checkpoint</span>
                                        <h3 class="h3 quiz-question-text" id="quiz-question"></h3>
                                    </div>
                                    <div class="quiz-options" id="quiz-options"></div>
                                    <div id="quiz-feedback" class="quiz-feedback hidden"></div>
                                    <button id="submit-quiz" class="btn btn-primary w-100">Submit Answer</button>
                                </div>
                            </div>

                            <!-- Completion Overlay -->
                            <div id="completion-overlay" class="quiz-overlay hidden">
                                <div class="quiz-card glass text-center">
                                    <ion-icon name="checkmark-circle" class="success-icon"></ion-icon>
                                    <h3 class="h3">Lesson Complete!</h3>
                                    <p>You've mastered this module. Ready for the next one?</p>
                                    <button id="next-module-btn" class="btn btn-primary">Continue to Next Lesson</button>
                                </div>
                            </div>
                        </div>

                        <div class="course-panel no-border mt-4">
                            <h2 class="h3" id="active-module-title">Select a lesson</h2>
                            <p class="course-description" id="module-description">
                                Watch the lesson video and complete the quick checkpoint quiz to verify your progress and unlock the next module.
                            </p>
                        </div>
                    </div>

                    <!-- Sidebar Column -->
                    <div class="course-unified-right">
                        <div class="course-panel sidebar-panel">
                            <h2 class="h3 panel-title">Learning Path Content</h2>
                            
                            <ul class="course-accordion module-list">
                                @foreach($path['modules'] as $index => $module)
                                    @php
                                        $isCompleted = in_array($index, $completedModules);
                                        $isLocked = $index > 0 && !in_array($index - 1, $completedModules);
                                    @endphp
                                    <li class="module-item {{ $isCompleted ? 'completed' : '' }} {{ $isLocked ? 'locked' : '' }} {{ $index === 0 && !$isLocked ? 'active-module' : '' }}" 
                                        data-index="{{ $index }}"
                                        data-video-id="{{ $module['video_id'] }}"
                                        data-title="{{ $module['title'] }}"
                                        data-quiz='@json($module['quiz'])'>
                                        
                                        <div class="accordion-header lesson-row">
                                            <div class="accordion-title lesson-left">
                                                <div class="status-icon">
                                                    @if($isCompleted)
                                                        <ion-icon name="checkmark-circle" class="completed-icon"></ion-icon>
                                                    @elseif($isLocked)
                                                        <ion-icon name="lock-closed-outline" class="locked-icon"></ion-icon>
                                                    @else
                                                        <ion-icon name="play-outline" class="ready-icon"></ion-icon>
                                                    @endif
                                                </div>
                                                <div class="lesson-info">
                                                    <span class="part-name">{{ $module['title'] }}</span>
                                                    <span class="lesson-subtext">Module {{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                            <div class="accordion-meta">
                                                <ion-icon name="chevron-forward-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="course-panel sidebar-panel mt-4">
                            <h2 class="h3 panel-title">Path Focus</h2>
                            <div class="focus-card">
                                <div class="focus-icon-wrapper">
                                    <img src="{{ asset('fitlife-assets/images/' . $path['icon']) }}" alt="{{ $path['title'] }}">
                                </div>
                                <div class="focus-info">
                                    <h4 class="h4">{{ $path['focus'] }}</h4>
                                    <p>{{ $path['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </article>
</main>

<style>
/* Refined Player & Interactive Styles */
.video-stage { 
    position: relative; 
    background: #000; 
    border-radius: 12px; 
    overflow: hidden;
    box-shadow: 0 20px 40px -15px rgba(0,0,0,0.2);
}

.player-ratio { position: relative; padding-top: 56.25%; }
#youtube-player, .player-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

.player-overlay {
    background: #1e293b;
    display: flex; align-items: center; justify-content: center;
    color: #fff; z-index: 10;
}

.overlay-content { text-align: center; }
.overlay-content ion-icon { font-size: 60px; color: rgba(255,255,255,0.3); margin-bottom: 10px; }

.quiz-overlay {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.85);
    display: flex; align-items: center; justify-content: center;
    z-index: 20; backdrop-filter: blur(10px);
}

.quiz-card {
    background: #fff; padding: 35px; border-radius: 20px;
    width: 90%; max-width: 450px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
}

.quiz-badge {
    display: inline-block; background: #eef2ff; color: #4f46e5;
    padding: 4px 12px; border-radius: 100px;
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    margin-bottom: 12px;
}

.quiz-options { display: flex; flex-direction: column; gap: 10px; margin-block: 20px; }

.quiz-option {
    padding: 12px 18px; border: 1px solid #e2e8f0;
    border-radius: 10px; cursor: pointer;
    transition: all 0.2s; font-size: 0.95rem;
}

.quiz-option:hover { border-color: #4f46e5; background: #f8fafc; }
.quiz-option.selected { border-color: #4f46e5; background: #eef2ff; }
.quiz-option.correct { border-color: #22c55e; background: #f0fdf4; color: #166534; }
.quiz-option.wrong { border-color: #ef4444; background: #fef2f2; color: #991b1b; }

.success-icon { font-size: 64px; color: #22c55e; margin-bottom: 15px; }

/* Sidebar Refinements */
.module-list { border: none !important; margin-top: 15px; }

.module-item {
    border: 1px solid #f1f5f9; border-radius: 12px;
    margin-bottom: 8px; cursor: pointer; transition: all 0.25s;
    background: #fff; list-style: none;
}

.module-item:hover:not(.locked) { transform: translateX(5px); border-color: #4f46e5; }
.module-item.active-module { border-color: #4f46e5; background: #f8fafc; }
.module-item.locked { cursor: not-allowed; opacity: 0.6; background: #f1f5f9; }

.lesson-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 15px; }
.lesson-left { display: flex; align-items: center; gap: 12px; }

.status-icon {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: #f8fafc; font-size: 1.2rem;
}

.completed-icon { color: #22c55e; }
.ready-icon { color: #4f46e5; }
.locked-icon { color: #94a3b8; }

.lesson-info { display: flex; flex-direction: column; }
.part-name { font-weight: 600; font-size: 0.95rem; color: #1e293b; }
.lesson-subtext { font-size: 0.75rem; color: #64748b; }

.progress-container-inline { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }

.focus-card { display: flex; align-items: center; gap: 15px; }
.focus-icon-wrapper { width: 60px; height: 60px; flex-shrink: 0; }
.focus-icon-wrapper img { width: 100%; height: 100%; object-fit: contain; }
.focus-info .h4 { font-size: 1rem; margin-bottom: 4px; }
.focus-info p { font-size: 0.85rem; color: #64748b; line-height: 1.4; }

.mt-4 { margin-top: 1.5rem; }
.text-center { text-align: center; }
.hidden { display: none !important; }
</style>

<script src="https://www.youtube.com/iframe_api"></script>
<script>
let player;
let activeModuleIndex = null;
let activeQuiz = null;
let selectedOption = null;

const pathSlug = @json($path['slug']);
const completedModules = @json($completedModules);

function onYouTubeIframeAPIReady() {
    player = new YT.Player('youtube-player', {
        height: '100%',
        width: '100%',
        playerVars: { 'autoplay': 0, 'rel': 0, 'modestbranding': 1, 'origin': window.location.origin },
        events: {
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.ENDED) {
        if (!completedModules.includes(activeModuleIndex)) {
            showQuiz();
        } else {
            showCompletion();
        }
    }
}

// Event Delegation for Module Selection
document.querySelector('.module-list').addEventListener('click', (e) => {
    const item = e.target.closest('.module-item');
    if (!item || item.classList.contains('locked')) return;

    const index = parseInt(item.dataset.index);
    const videoId = item.dataset.videoId;
    const title = item.dataset.title;
    const quiz = JSON.parse(item.dataset.quiz);

    loadModule(index, videoId, title, quiz);
});

function loadModule(index, videoId, title, quiz) {
    activeModuleIndex = index;
    activeQuiz = quiz;
    
    // Update UI
    document.querySelectorAll('.module-item').forEach(i => i.classList.remove('active-module'));
    document.querySelector(`.module-item[data-index="${index}"]`).classList.add('active-module');
    document.getElementById('active-module-title').innerText = title;
    document.getElementById('player-placeholder').classList.add('hidden');
    
    // Reset Overlays
    document.getElementById('quiz-overlay').classList.add('hidden');
    document.getElementById('completion-overlay').classList.add('hidden');
    
    // Load Video
    player.loadVideoById(videoId);
    player.playVideo();
}

function showQuiz() {
    document.getElementById('quiz-question').innerText = activeQuiz.question;
    const optionsCont = document.getElementById('quiz-options');
    optionsCont.innerHTML = '';
    selectedOption = null;

    activeQuiz.options.forEach((opt, idx) => {
        const div = document.createElement('div');
        div.className = 'quiz-option';
        div.innerText = opt;
        div.addEventListener('click', () => {
            document.querySelectorAll('.quiz-option').forEach(el => el.classList.remove('selected'));
            div.classList.add('selected');
            selectedOption = idx;
        });
        optionsCont.appendChild(div);
    });

    document.getElementById('quiz-overlay').classList.remove('hidden');
    document.getElementById('quiz-feedback').classList.add('hidden');
}

document.getElementById('submit-quiz').addEventListener('click', () => {
    if (selectedOption === null) return;

    const options = document.querySelectorAll('.quiz-option');
    if (selectedOption === activeQuiz.answer) {
        options[selectedOption].classList.add('correct');
        handleCompletion();
    } else {
        options[selectedOption].classList.add('wrong');
        options[activeQuiz.answer].classList.add('correct');
        const feedback = document.getElementById('quiz-feedback');
        feedback.innerText = "Incorrect. Watch the video once more!";
        feedback.className = "quiz-feedback error py-3 text-red-600 font-bold";
        feedback.classList.remove('hidden');
    }
});

function handleCompletion() {
    fetch("{{ route('paths.progress') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            path_slug: pathSlug,
            module_index: activeModuleIndex
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            completedModules.push(activeModuleIndex);
            updateSideBarStatus(activeModuleIndex);
            setTimeout(showCompletion, 800);
        }
    });
}

function showCompletion() {
    document.getElementById('quiz-overlay').classList.add('hidden');
    document.getElementById('completion-overlay').classList.remove('hidden');
}

function updateSideBarStatus(index) {
    const item = document.querySelector(`.module-item[data-index="${index}"]`);
    item.classList.add('completed');
    item.querySelector('.status-icon').innerHTML = '<ion-icon name="checkmark-circle" class="completed-icon"></ion-icon>';
    
    // Unlock next
    const next = document.querySelector(`.module-item[data-index="${index + 1}"]`);
    if (next) {
        next.classList.remove('locked');
        next.querySelector('.status-icon').innerHTML = '<ion-icon name="play-outline" class="ready-icon"></ion-icon>';
    }

    // Update Progress Bar
    const total = {{ count($path['modules']) }};
    const done = completedModules.length;
    const pct = Math.round((done / total) * 100);
    document.getElementById('progress-percent-header').innerText = pct + '%';
}

document.getElementById('next-module-btn').addEventListener('click', () => {
    const nextIndex = activeModuleIndex + 1;
    const nextItem = document.querySelector(`.module-item[data-index="${nextIndex}"]`);
    if (nextItem) {
        nextItem.click();
    } else {
        window.location.href = "{{ route('paths') }}";
    }
});
</script>
@endsection
