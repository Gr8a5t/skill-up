<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserLessonProgress;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FitlifeController;

class DashboardProgress extends Component
{
    public $stats = [];
    public $lessons = [];
    public $activityMap = [];
    public $mentors = [];
    public $continueWatching = [];

    protected $listeners = ['progressUpdated' => '$refresh'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();
        
        $fitlife = new FitlifeController();
        $allCourses = $fitlife->getWebDevCourses();

        // 1. Get Progress & Lessons
        $queryRef = function ($q) use ($userId, $sessionId) {
            if ($userId) $q->where('user_id', $userId);
            else         $q->where('session_id', $sessionId);
        };

        $progressedSlugs = UserLessonProgress::where($queryRef)
            ->select('course_slug')
            ->distinct()
            ->pluck('course_slug')
            ->toArray();

        $recentProgress = UserLessonProgress::where($queryRef)
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $this->lessons = $recentProgress->map(function ($rec) use ($allCourses) {
            $courseData = collect($allCourses)->firstWhere('slug', $rec->course_slug);
            $pct = $rec->total_seconds > 0
                ? min(100, round(($rec->progress_seconds / $rec->total_seconds) * 100))
                : 0;
            return [
                'course_slug' => $rec->course_slug,
                'video_id'    => $rec->video_id,
                'category'    => $courseData['category'] ?? 'Course',
                'title'       => $courseData['title'] ?? $rec->course_slug,
                'progress'    => $pct,
                'updated'     => $rec->updated_at,
            ];
        })->toArray();

        // 2. Stats
        $totalVideosStarted = UserLessonProgress::where($queryRef)->count();
        $completedVideos = UserLessonProgress::where($queryRef)->where('is_completed', true)->count();
        $overallPct = $totalVideosStarted > 0 ? round(($completedVideos / $totalVideosStarted) * 100) : 0;

        $this->stats = [
            'courses_started'  => count($progressedSlugs),
            'videos_completed' => $completedVideos,
            'overall_pct'      => $overallPct,
        ];

        // 3. Activity Map
        $activityRaw = UserLessonProgress::where($queryRef)
            ->where('updated_at', '>=', now()->subDays(365))
            ->selectRaw('DATE(updated_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $this->activityMap = [];
        for ($i = 364; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $this->activityMap[$date] = $activityRaw[$date] ?? 0;
        }

        // 4. Other data
        $this->continueWatching = collect($allCourses)->take(6)->map(fn($c) => [
            'slug'     => $c['slug'],
            'title'    => $c['title'],
            'category' => $c['category'],
            'icon'     => $c['icon'],
            'color'    => $c['color'],
        ])->values()->toArray();

        $this->mentors = [
            ['name' => 'Padhang Satrio',   'role' => 'Mentor', 'avatar' => 'PS'],
            ['name' => 'Zakir Horizontal', 'role' => 'Mentor', 'avatar' => 'ZH'],
            ['name' => 'Leonardo samsul',  'role' => 'Mentor', 'avatar' => 'LS'],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-progress');
    }
}
