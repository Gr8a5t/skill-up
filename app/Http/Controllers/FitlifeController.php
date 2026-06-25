<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FitlifeController extends Controller
{
    public function index()
    {
        $classes = $this->skillUpPaths();

        $blogs = [
            [
                'title' => 'SkillUp launches collaborative learning prompts',
                'image' => 'blog-1.jpg',
                'alt' => 'skillup collaborative learning',
                'date' => '7 July 2022',
                'datetime' => '2022-07-07',
                'excerpt' => 'New weekly prompts keep learners accountable, with conversation guides so you can grow inside a supportive micro-community.',
            ],
            [
                'title' => 'How SkillUp curates the right first skill',
                'image' => 'blog-2.jpg',
                'alt' => 'skillup first skill',
                'date' => '7 July 2022',
                'datetime' => '2022-07-07',
                'excerpt' => 'We mapped the things students ask for most and bundled them into tidy starting points that blend coding, design, and soft-skill micro-tasks.',
            ],
            [
                'title' => 'Monthly mentor check-ins keep practice real',
                'image' => 'blog-3.jpg',
                'alt' => 'skillup mentors',
                'date' => '7 July 2022',
                'datetime' => '2022-07-07',
                'excerpt' => 'Mentor minutes help you iterate on your portfolio, hear honest feedback, and translate practice into job-ready stories.',
            ],
        ];

        return \Inertia\Inertia::render('Home', compact('classes', 'blogs'));
    }

    public function about()
    {
        return \Inertia\Inertia::render('About');
    }

    public function getWebDevCourses()
    {
        return [
            [
                'slug' => 'html-basics',
                'title' => 'HTML Crash Course for Beginners',
                'level' => 'Beginner',
                'category' => 'Frontend',
                'tags' => ['HTML', 'Web Design'],
                'color' => '#fcf6f5',
                'icon' => 'logo-html5',
                'playlist_id' => 'PL4cUxeGkcC9ivBf_eKCPIAYXWzLlPAm6G'
            ],
            [
                'slug' => 'css-styling',
                'title' => 'CSS Crash Course',
                'level' => 'Beginner',
                'category' => 'Frontend',
                'tags' => ['CSS', 'Styling'],
                'color' => '#f5f9fc',
                'icon' => 'logo-css3',
                'playlist_id' => 'PL4cUxeGkcC9gQeDH6xYhmdy-dbOPTgO3C'
            ],
            [
                'slug' => 'modern-javascript',
                'title' => 'Modern JavaScript Tutorial',
                'level' => 'Medium',
                'category' => 'Frontend',
                'tags' => ['JavaScript', 'ES6'],
                'color' => '#fcfbf5',
                'icon' => 'logo-javascript',
                'playlist_id' => 'PL4cUxeGkcC9haQlqdCQyYmL_27TesCGPC'
            ],
            [
                'slug' => 'php-fundamentals',
                'title' => 'PHP Tutorial for Beginners',
                'level' => 'Medium',
                'category' => 'Backend',
                'tags' => ['PHP', 'Backend'],
                'color' => '#f7f5fc',
                'icon' => 'server-outline',
                'playlist_id' => 'PL4cUxeGkcC9gksOX3Kd9KPo-O68ncT05o'
            ],
            [
                'slug' => 'laravel-mastery',
                'title' => 'Laravel Crash Course',
                'level' => 'Advance',
                'category' => 'Backend',
                'tags' => ['Laravel', 'PHP'],
                'color' => '#fcf5f5',
                'icon' => 'cube-outline',
                'playlist_id' => 'PL0eyrZgxdwhy7Woo2VRRDMmTXXYT_iaYO'
            ],
            [
                'slug' => 'react-beginners',
                'title' => 'React for Beginners',
                'level' => 'Advance',
                'category' => 'Frontend',
                'tags' => ['React', 'JavaScript'],
                'color' => '#f5fbfc',
                'icon' => 'logo-react',
                'playlist_id' => 'PL4cUxeGkcC9gZD-Tvwfod2gaISzfRiP9d'
            ],
            [
                'slug' => 'ai-agents-intro',
                'title' => 'Building AI Agents',
                'level' => 'Advance',
                'category' => 'Artificial Intelligence',
                'tags' => ['AI', 'Agents'],
                'color' => '#f9f5fc',
                'icon' => 'hardware-chip-outline',
                'playlist_id' => 'PLAqhIrjkxbuWI23v9cThsA9GvCAUhRvKZ'
            ]
        ];
    }

    public function courses(Request $request)
    {
        $search = $request->input('search');

        // Pull published DB courses first
        $dbCourses = Course::where('is_published', true)->with('lessons')->get()->map(function ($c) {
            return [
                'slug'        => $c->slug,
                'title'       => $c->title,
                'level'       => $c->level,
                'category'    => $c->category,
                'tags'        => [$c->category, $c->level],
                'color'       => $c->color,
                'icon'        => $c->icon,
                'playlist_id' => $c->playlist_id,
                '_source'     => 'db',
            ];
        })->keyBy('slug')->toArray();

        // Merge with hardcoded (hardcoded fills in if slug not in DB yet)
        $hardcoded = collect($this->getWebDevCourses())->keyBy('slug')->toArray();
        $allCourses = array_values(array_merge($hardcoded, $dbCourses));

        if ($search) {
            $allCourses = array_values(array_filter($allCourses, function ($course) use ($search) {
                return str_contains(strtolower($course['title']), strtolower($search)) ||
                       str_contains(strtolower($course['category']), strtolower($search)) ||
                       str_contains(strtolower(implode(' ', $course['tags'] ?? [])), strtolower($search));
            }));
        }

        $courses = $allCourses;
        return \Inertia\Inertia::render('Courses', compact('courses'));
    }

    public function courseLearn(string $slug)
    {
        $youtubeKey = config('services.youtube.key');

        // Try DB first, fall back to hardcoded array
        $dbCourse = Course::where('slug', $slug)->with('lessons')->first();
        $allCourses = $this->getWebDevCourses();
        $hardcodedData = collect($allCourses)->firstWhere('slug', $slug) ?? $allCourses[0];

        // Merge: prefer DB values where set
        $playlistId = $dbCourse?->playlist_id ?? $hardcodedData['playlist_id'] ?? null;

        $items = [];
        $apiError = null;

        if ($playlistId && !empty($youtubeKey)) {
            try {
                $response = Http::timeout(8)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                    'part'       => 'snippet',
                    'playlistId' => $playlistId,
                    'maxResults' => 20,
                    'key'        => $youtubeKey,
                ]);
                $json = $response->json();
                if (isset($json['error'])) {
                    $apiError = $json['error']['message'] ?? 'Unknown YouTube API error';
                    Log::error('[CourseLearn] YouTube API error: ' . $apiError);
                } else {
                    $items = $json['items'] ?? [];
                }
            } catch (\Exception $e) {
                $apiError = $e->getMessage();
                Log::error('[CourseLearn] HTTP exception: ' . $apiError);
            }
        } elseif (empty($youtubeKey)) {
            $apiError = 'YOUTUBE_API_KEY is not configured.';
            Log::error('[CourseLearn] ' . $apiError);
        }

        // Build recap & concepts — prefer DB content
        $recap = $dbCourse?->recap
            ?? 'In this course, we will build a modern application from scratch. You will learn about core syntax, state, context, and more.';

        $concepts = $dbCourse?->key_concepts
            ?? [
                'Understanding ' . ($dbCourse?->title ?? $hardcodedData['title']) . ' fundamentals',
                'Managing state and side effects',
                'Applying styles and components',
                'Deploying to production',
            ];

        $firstVideoId = count($items) > 0
            ? ($items[0]['snippet']['resourceId']['videoId'] ?? 'SqcY0GlETPk')
            : 'SqcY0GlETPk';

        $course = [
            'title'         => $dbCourse?->title ?? $hardcodedData['title'],
            'category'      => $dbCourse?->category ?? $hardcodedData['category'],
            'level'         => $dbCourse?->level ?? $hardcodedData['level'],
            'lessons_count' => count($items),
            'duration'      => 'Self Paced',
            'video_id'      => $firstVideoId,
            'recap'         => $recap,
            'concepts'      => $concepts,
            'source_files_url' => $dbCourse?->source_files_url,
            'cheatsheet_url'   => $dbCourse?->cheatsheet_url,
        ];

        $activeVideoId = request('v', $course['video_id']);
        $course['video_id'] = $activeVideoId;

        // Progress records for this user/session
        $progressRecords = [];
        if (auth()->check()) {
            $progressRecords = \App\Models\UserLessonProgress::where('course_slug', $slug)
                ->where('user_id', auth()->id())
                ->get()->keyBy('video_id');
        } else {
            $progressRecords = \App\Models\UserLessonProgress::where('course_slug', $slug)
                ->where('session_id', session()->getId())
                ->get()->keyBy('video_id');
        }

        $lessons = [];

        if (count($items) > 0) {
            // YouTube API succeeded — use playlist items
            foreach ($items as $item) {
                $vidId = $item['snippet']['resourceId']['videoId'];
                $progressRec = $progressRecords[$vidId] ?? null;
                $pct = 0;
                if ($progressRec && $progressRec->total_seconds > 0) {
                    $pct = min(100, round(($progressRec->progress_seconds / $progressRec->total_seconds) * 100));
                }
                $lessons[] = [
                    'video_id' => $vidId,
                    'title'    => $item['snippet']['title'],
                    'time'     => 'Video',
                    'progress' => $pct,
                    'active'   => ($vidId === $activeVideoId),
                ];
            }
        } elseif ($dbCourse && $dbCourse->lessons->isNotEmpty()) {
            // YouTube API failed but we have DB lessons — use them
            $firstDbId = $dbCourse->lessons->first()->video_id;
            if ($activeVideoId === 'SqcY0GlETPk') {
                $activeVideoId = $firstDbId;
                $course['video_id'] = $firstDbId;
            }

            foreach ($dbCourse->lessons as $lesson) {
                $vidId = $lesson->video_id;
                $progressRec = $progressRecords[$vidId] ?? null;
                $pct = 0;
                if ($progressRec && $progressRec->total_seconds > 0) {
                    $pct = min(100, round(($progressRec->progress_seconds / $progressRec->total_seconds) * 100));
                }
                $lessons[] = [
                    'video_id' => $vidId,
                    'title'    => $lesson->title,
                    'time'     => 'Video',
                    'progress' => $pct,
                    'active'   => ($vidId === $activeVideoId),
                ];
            }
            $course['lessons_count'] = count($lessons);
        } else {
            // Last resort: hardcoded fallback
            $fallbackVideos = [
                'html-basics'       => 'it1rTvBcfRg',
                'css-styling'       => 'wRNinF7YQqQ',
                'modern-javascript' => 'W6NZfCO5SIk',
                'php-fundamentals'  => 'OK_JCtrrv-c',
                'laravel-mastery'   => 'Rz6SMgKrSYE',
                'react-beginners'   => 'SqcY0GlETPk',
                'ai-agents-intro'   => 'VMj-3S1tku0',
            ];
            $fallbackId = $fallbackVideos[$slug] ?? 'SqcY0GlETPk';
            $lessons = [
                ['video_id' => $fallbackId, 'title' => $hardcodedData['title'] . ' — Intro', 'time' => 'Video', 'progress' => 0, 'active' => true],
            ];
            $course['video_id'] = $fallbackId;
            $activeVideoId = $fallbackId;
        }

        $comments = \App\Models\CourseComment::where('course_slug', $slug)
            ->whereNull('parent_id')
            ->with(['replies' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($comment) {
                $likedBy = is_array($comment->liked_by) ? $comment->liked_by : json_decode($comment->liked_by ?? '[]', true);
                $identifier = auth()->check() ? auth()->id() : session()->getId();
                
                $replies = $comment->replies->map(function($reply) use ($identifier) {
                    $replyLikedBy = is_array($reply->liked_by) ? $reply->liked_by : json_decode($reply->liked_by ?? '[]', true);
                    return [
                        'id' => $reply->id,
                        'user_id' => $reply->user_id,
                        'user_name' => $reply->user_name,
                        'avatar' => $reply->avatar,
                        'content' => $reply->content,
                        'created_at' => $reply->created_at->toIso8601String(),
                        'likes' => $reply->likes,
                        'is_liked' => in_array($identifier, $replyLikedBy),
                    ];
                });

                return [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'user_name' => $comment->user_name,
                    'avatar' => $comment->avatar,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toIso8601String(),
                    'likes' => $comment->likes,
                    'is_liked' => in_array($identifier, $likedBy),
                    'replies' => $replies,
                ];
            });

        return \Inertia\Inertia::render('CourseLearn', compact('course', 'lessons', 'slug', 'comments'));
    }

    public function updateCourseProgress(Request $request)
    {
        $request->validate([
            'course_slug' => 'required|string',
            'video_id' => 'required|string',
            'progress_seconds' => 'required|numeric',
            'total_seconds' => 'required|numeric',
        ]);

        $userId = auth()->id();
        $sessionId = session()->getId();

        $progress = \App\Models\UserLessonProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'course_slug' => $request->course_slug,
                'video_id' => $request->video_id,
            ],
            [
                'progress_seconds' => $request->progress_seconds,
                'total_seconds' => $request->total_seconds,
                'is_completed' => ($request->progress_seconds >= $request->total_seconds * 0.95), // completed if 95% watched
            ]
        );

        return response()->json(['status' => 'success', 'progress' => $progress]);
    }

    public function showCourse(string $slug)
    {
        // Try DB first, fall back to hardcoded array
        $dbCourse = Course::where('slug', $slug)->first();
        $hardcodedCourses = $this->getWebDevCourses();
        $courseData = collect($hardcodedCourses)->firstWhere('slug', $slug);

        if (!$dbCourse && !$courseData) {
            abort(404);
        }

        $course = [
            'slug'        => $dbCourse?->slug ?? $courseData['slug'] ?? $slug,
            'title'       => $dbCourse?->title ?? $courseData['title'] ?? 'Course Title',
            'description' => $dbCourse?->recap ?? 'A comprehensive course on ' . ($dbCourse?->title ?? $courseData['title']),
            'excerpt'     => Str::limit($dbCourse?->recap ?? 'Start learning now.', 120),
            'level'       => $dbCourse?->level ?? $courseData['level'] ?? 'Beginner',
            'category'    => $dbCourse?->category ?? $courseData['category'] ?? 'Development',
            'lessons'     => $dbCourse ? $dbCourse->lessons->count() : 1,
            'image'       => asset('fitlife-assets/images/hero-banner.png'), // Default banner
            'coursera_link' => '#',
        ];

        $learningPoints = $dbCourse?->key_concepts ?? ['Core fundamentals', 'Best practices', 'Hands-on projects'];

        return \Inertia\Inertia::render('CourseDetail', compact('course', 'learningPoints'));
    }

    public function paths()
    {
        return \Inertia\Inertia::render('Paths');
    }

    public function learn(string $slug)
    {
        $paths = $this->skillUpPaths();
        $path = collect($paths)->firstWhere('slug', $slug);

        if (!$path) {
            abort(404);
        }

        $sessionId = session()->getId();
        $userId = auth()->id();

        $completedModules = DB::table('user_path_progress')
            ->where('path_slug', $slug)
            ->where(function($q) use ($userId, $sessionId) {
                if ($userId) $q->where('user_id', $userId);
                else $q->where('session_id', $sessionId);
            })
            ->pluck('module_index')
            ->toArray();

        return redirect()->route('paths');
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'path_slug' => 'nullable|string',
            'course_slug' => 'nullable|string',
            'module_index' => 'nullable|integer',
            'video_id' => 'nullable|string',
            'progress_seconds' => 'nullable|numeric',
            'total_seconds' => 'nullable|numeric',
        ]);

        $sessionId = session()->getId();
        $userId = auth()->id();

        // Handle Course/Video Progress (YouTube)
        if ($request->course_slug && $request->video_id) {
            $progress = \App\Models\UserLessonProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'session_id' => $userId ? null : $sessionId,
                    'course_slug' => $request->course_slug,
                    'video_id' => $request->video_id,
                ],
                [
                    'progress_seconds' => $request->progress_seconds,
                    'total_seconds' => $request->total_seconds ?? 0,
                    'is_completed' => ($request->progress_seconds >= ($request->total_seconds ?? 1) * 0.95),
                    'updated_at' => now(),
                ]
            );
            return response()->json(['status' => 'success', 'progress' => $progress]);
        }

        // Handle Path Module Progress
        if ($request->path_slug && isset($request->module_index)) {
            DB::table('user_path_progress')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'session_id' => $userId ? null : $sessionId,
                    'path_slug' => $request->path_slug,
                    'module_index' => $request->module_index,
                ],
                [
                    'completed' => true,
                    'updated_at' => now(),
                ]
            );
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Invalid data'], 400);
    }

    private function skillUpPaths(): array
    {
        return [
            [
                'title' => 'Coding Fundamentals',
                'slug' => 'coding-fundamentals',
                'description' => 'Build a habit with short labs that cover HTML, CSS, and JavaScript during the first two weeks.',
                'duration' => '4 weeks',
                'focus' => 'Frontend basics',
                'image' => 'class-1.jpg',
                'icon' => 'class-icon-1.png',
                'statusLabel' => 'Path confidence',
                'progress' => 0,
                'modules' => [
                    [
                        'title' => 'Welcome to the Web',
                        'video_id' => 'FQdaUv95mR8',
                        'quiz' => [
                            'question' => 'What does HTML stand for?',
                            'options' => ['HyperText Markup Language', 'High Tech Modern Language', 'HyperTransfer Mode List'],
                            'answer' => 0
                        ]
                    ],
                    [
                        'title' => 'Styling with CSS',
                        'video_id' => 'yfoY53QXEnI',
                        'quiz' => [
                            'question' => 'Which property is used to change the background color?',
                            'options' => ['color', 'background-color', 'bgcolor'],
                            'answer' => 1
                        ]
                    ],
                    [
                        'title' => 'Interactivity with JS',
                        'video_id' => 'W6NZfCO5SIk',
                        'quiz' => [
                            'question' => 'Which keyword is used to declare a variable in modern JS?',
                            'options' => ['var', 'let', 'set'],
                            'answer' => 1
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Product Design Sprint',
                'slug' => 'product-design-sprint',
                'description' => 'Sketch, prototype, and test a product idea while learning the language of UX and product thinking.',
                'duration' => '5 weeks',
                'focus' => 'Design + research',
                'image' => 'class-2.jpg',
                'icon' => 'class-icon-2.png',
                'statusLabel' => 'Path confidence',
                'progress' => 0,
                'modules' => [
                    [
                        'title' => 'Understanding UX Design',
                        'video_id' => 'vK8YshV2SVs',
                        'quiz' => [
                            'question' => 'What does UX stand for?',
                            'options' => ['User Experience', 'User eXchange', 'Universal Design'],
                            'answer' => 0
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Career-Ready Portfolio',
                'slug' => 'career-ready-portfolio',
                'description' => 'Document your projects, polish your story, and publish an online showcase that recruiters can scan.',
                'duration' => '3 weeks',
                'focus' => 'Storytelling',
                'image' => 'class-3.jpg',
                'icon' => 'class-icon-3.png',
                'statusLabel' => 'Path confidence',
                'progress' => 0,
                'modules' => [
                    [
                        'title' => 'Building Your Brand',
                        'video_id' => '8-V79rZ-X40',
                        'quiz' => [
                            'question' => 'What is the most important part of a portfolio?',
                            'options' => ['Number of projects', 'Case studies and process', 'A fancy logo'],
                            'answer' => 1
                        ]
                    ]
                ]
            ],
            [
                'title' => 'SkillUp Club',
                'slug' => 'skillup-club',
                'description' => 'Weekly accountability check-ins plus quick prompts to keep you practicing even when motivation dips.',
                'duration' => 'Ongoing',
                'focus' => 'Community practice',
                'image' => 'class-4.jpg',
                'icon' => 'class-icon-4.png',
                'statusLabel' => 'Path confidence',
                'progress' => 0,
                'modules' => [
                    [
                        'title' => 'Group Accountability',
                        'video_id' => 'I8_DNRc0Tvg',
                        'quiz' => [
                            'question' => 'How often should you check in?',
                            'options' => ['Daily', 'Weekly', 'Monthly'],
                            'answer' => 1
                        ]
                    ]
                ]
            ],
        ];
    }

    public function dashboard()
    {
        return \Inertia\Inertia::render('Dashboard', $this->getDashboardData());
    }

    public function getDashboardData()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();
        $allCourses = $this->getWebDevCourses();

        $queryRef = function ($q) use ($userId, $sessionId) {
            if ($userId) $q->where('user_id', $userId);
            else         $q->where('session_id', $sessionId);
        };

        $progressedSlugs = \App\Models\UserLessonProgress::where($queryRef)
            ->select('course_slug')
            ->distinct()
            ->pluck('course_slug')
            ->toArray();

        $recentProgress = \App\Models\UserLessonProgress::where($queryRef)
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $lessons = $recentProgress->map(function ($rec) use ($allCourses) {
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
                'updated'     => $rec->updated_at ? $rec->updated_at->toIso8601String() : null,
            ];
        })->toArray();

        $totalVideosStarted = \App\Models\UserLessonProgress::where($queryRef)->count();
        $completedVideos = \App\Models\UserLessonProgress::where($queryRef)->where('is_completed', true)->count();
        $overallPct = $totalVideosStarted > 0 ? round(($completedVideos / $totalVideosStarted) * 100) : 0;

        $stats = [
            'courses_started'  => count($progressedSlugs),
            'videos_completed' => $completedVideos,
            'overall_pct'      => $overallPct,
        ];

        $activityRaw = \App\Models\UserLessonProgress::where($queryRef)
            ->where('updated_at', '>=', now()->subDays(365))
            ->selectRaw('DATE(updated_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $activityMap = [];
        for ($i = 364; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityMap[$date] = $activityRaw[$date] ?? 0;
        }

        $continueWatching = collect($allCourses)->take(6)->map(fn($c) => [
            'slug'     => $c['slug'],
            'title'    => $c['title'],
            'category' => $c['category'],
            'icon'     => $c['icon'],
            'color'    => $c['color'],
        ])->values()->toArray();

        $mentors = [
            ['name' => 'Padhang Satrio',   'role' => 'Mentor', 'avatar' => 'PS'],
            ['name' => 'Zakir Horizontal', 'role' => 'Mentor', 'avatar' => 'ZH'],
            ['name' => 'Leonardo samsul',  'role' => 'Mentor', 'avatar' => 'LS'],
        ];

        return [
            'stats' => $stats,
            'lessons' => $lessons,
            'activityMap' => $activityMap,
            'continueWatching' => $continueWatching,
            'mentors' => $mentors,
        ];
    }

    public function chats(Request $request)
    {
        return \Inertia\Inertia::render('Chats', $this->getChatsData($request));
    }

    public function getChatsData(Request $request)
    {
        $userId = auth()->id();
        $recipientIdHash = $request->query('user_id');

        if (!$recipientIdHash) {
            $latestMessage = \App\Models\ChatMessage::where('sender_id', $userId)
                ->orWhere('recipient_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestMessage) {
                $recId = $latestMessage->sender_id === $userId 
                    ? $latestMessage->recipient_id 
                    : $latestMessage->sender_id;
                $recipientIdHash = \App\Utils\HashId::encode($recId);
            }
        }

        $latestMessages = \App\Models\ChatMessage::whereIn('id', function ($query) use ($userId) {
            $query->select(\Illuminate\Support\Facades\DB::raw('MAX(id)'))
                ->from('chat_messages')
                ->where('sender_id', $userId)
                ->orWhere('recipient_id', $userId)
                ->groupBy(\Illuminate\Support\Facades\DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN recipient_id ELSE sender_id END'));
        })
        ->select('id', \Illuminate\Support\Facades\DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN recipient_id ELSE sender_id END as contact_id'), 'created_at', 'message', 'is_read', 'sender_id')
        ->orderBy('created_at', 'desc')
        ->get();

        $conversations = [];
        foreach ($latestMessages as $msg) {
            $user = \App\Models\User::find($msg->contact_id);
            if (!$user) continue;

            $unreadCount = \App\Models\ChatMessage::where('sender_id', $user->id)
                ->where('recipient_id', $userId)
                ->where('is_read', false)
                ->count();

            $conversations[] = [
                'id' => \App\Utils\HashId::encode($user->id),
                'name' => $user->name,
                'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f0ebff&color=8e54e9',
                'message' => $msg->message,
                'time' => $msg->created_at->diffForHumans(null, true),
                'unread' => $unreadCount > 0,
                'unread_count' => $unreadCount,
                'is_online' => $user->isOnline(),
            ];
        }

        $messages = collect();
        $activeRecipient = null;

        if ($recipientIdHash) {
            $decoded = \App\Utils\HashId::decode($recipientIdHash);
            if (!empty($decoded)) {
                $recipientId = $decoded[0];
                $activeRecipient = \App\Models\User::find($recipientId);

                if ($activeRecipient) {
                    \App\Models\ChatMessage::where('sender_id', $recipientId)
                        ->where('recipient_id', $userId)
                        ->where('is_read', false)
                        ->update(['is_read' => true]);

                    $messages = \App\Models\ChatMessage::where(function ($q) use ($recipientId, $userId) {
                            $q->where('sender_id', $userId)->where('recipient_id', $recipientId);
                        })->orWhere(function ($q) use ($recipientId, $userId) {
                            $q->where('sender_id', $recipientId)->where('recipient_id', $userId);
                        })
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->map(function($msg) {
                            return [
                                'id' => $msg->id,
                                'sender_id' => $msg->sender_id,
                                'recipient_id' => $msg->recipient_id,
                                'message' => $msg->message,
                                'created_at' => $msg->created_at->toIso8601String(),
                            ];
                        });
                }
            }
        }

        return [
            'conversations' => $conversations,
            'messages' => $messages,
            'activeRecipient' => $activeRecipient ? [
                'id' => $activeRecipient->id,
                'name' => $activeRecipient->name,
                'avatar' => $activeRecipient->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($activeRecipient->name) . '&background=f0ebff&color=8e54e9',
                'is_online' => $activeRecipient->isOnline(),
            ] : null,
        ];
    }

    public function sendMessage(Request $request)
    {
        $userId = auth()->id();
        $recipientId = $request->input('recipient_id');
        $text = $request->input('message');

        if (!$recipientId || !$text) {
            return back()->withErrors(['message' => 'Invalid parameters']);
        }

        \App\Models\ChatMessage::create([
            'sender_id' => $userId,
            'recipient_id' => $recipientId,
            'message' => $text,
        ]);

        return back();
    }

    public function updateMessage(Request $request)
    {
        $userId = auth()->id();
        $messageId = $request->input('message_id');
        $text = $request->input('message');

        $message = \App\Models\ChatMessage::findOrFail($messageId);
        if ($message->sender_id !== $userId) {
            abort(403);
        }

        if ($message->created_at->diffInMinutes() >= 5) {
            return back()->withErrors(['message' => 'Time limit for editing (5m) has passed.']);
        }

        $message->update([
            'message' => $text,
        ]);

        return back();
    }

    public function deleteMessage($id)
    {
        $userId = auth()->id();
        $message = \App\Models\ChatMessage::findOrFail($id);
        
        if ($message->sender_id !== $userId) {
            abort(403);
        }

        $message->delete();

        return back();
    }

    public function forum()
    {
        $posts = \App\Models\ForumPost::with('user')->latest()->get()->map(function($post) {
            return [
                'id' => $post->id,
                'body' => $post->body,
                'tags' => $post->tags ?? ['General'],
                'created_at' => $post->created_at->toIso8601String(),
                'likes_count' => $post->likes_count ?? 0,
                'comments_count' => $post->comments_count ?? 0,
                'user' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                    'avatar' => $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=ffe4ef&color=ff4aa0',
                ]
            ];
        });

        return \Inertia\Inertia::render('Forum', [
            'posts' => $posts,
        ]);
    }

    public function createForumPost(Request $request)
    {
        $request->validate([
            'body' => 'required|min:3|max:2000',
        ]);

        $activeTab = $request->input('tab', 'For you');

        \App\Models\ForumPost::create([
            'user_id' => auth()->id(),
            'body' => $request->input('body'),
            'tags' => [$activeTab === 'For you' ? 'General' : $activeTab],
        ]);

        return back()->with('success', 'Post created successfully!');
    }

    public function submitComment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        \App\Models\CourseComment::create([
            'course_slug' => $slug,
            'user_id' => auth()->check() ? auth()->id() : null,
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest Student',
            'avatar' => auth()->check() 
                ? (auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name))
                : 'https://i.pravatar.cc/150?u=' . session()->getId(),
            'content' => $request->input('content'),
        ]);

        return back();
    }

    public function submitCommentReply(Request $request, $slug, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        \App\Models\CourseComment::create([
            'course_slug' => $slug,
            'parent_id' => $commentId,
            'user_id' => auth()->check() ? auth()->id() : null,
            'user_name' => auth()->check() ? auth()->user()->name : 'Guest Student',
            'avatar' => auth()->check() 
                ? (auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name))
                : 'https://i.pravatar.cc/150?u=' . session()->getId(),
            'content' => $request->input('content'),
        ]);

        return back();
    }

    public function likeComment(Request $request, $slug, $commentId)
    {
        $comment = \App\Models\CourseComment::findOrFail($commentId);
        $identifier = auth()->check() ? auth()->id() : session()->getId();
        $likedBy = is_array($comment->liked_by) ? $comment->liked_by : json_decode($comment->liked_by ?? '[]', true);
        
        if (!in_array($identifier, $likedBy)) {
            $likedBy[] = $identifier;
            $comment->liked_by = $likedBy;
            $comment->likes++;
        } else {
            $likedBy = array_diff($likedBy, [$identifier]);
            $comment->liked_by = array_values($likedBy);
            $comment->likes = max(0, $comment->likes - 1);
        }
        $comment->save();

        return back();
    }

    public function updateComment(Request $request, $slug, $commentId)
    {
        $comment = \App\Models\CourseComment::findOrFail($commentId);
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($comment->created_at->diffInMinutes() >= 5) {
            return back()->withErrors(['comment' => 'Time limit for editing (5m) has passed.']);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return back();
    }

    public function deleteComment($slug, $commentId)
    {
        $comment = \App\Models\CourseComment::findOrFail($commentId);
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete replies too
        $comment->replies()->delete();
        $comment->delete();

        return back();
    }

    public function courseAiChat(Request $request, $slug)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        $course = collect($this->getWebDevCourses())->firstWhere('slug', $slug);
        if (!$course) abort(404);

        $title = $course['title'] ?? 'Course';
        $recap = $course['recap'] ?? '';
        $concepts = implode(', ', $course['concepts'] ?? []);

        $courseContext = "You are an AI learning assistant exclusively for the course titled '{$title}'. " .
                         "Course Summary: {$recap}. Key Concepts covered: {$concepts}. " .
                         "Your only job is to answer questions related to this specific course and its concepts. " .
                         "If a user asks a question entirely unrelated to the course topic, politely decline to answer, " .
                         "stating that you are here specifically to help with '{$title}'. " .
                         "IMPORTANT: If you provide any websites or URLs, you MUST format them as valid clickable markdown links (e.g., [Website Name](https://example.com)). " .
                         "Keep your answers concise, helpful, and educational.";

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'role' => 'assistant',
                'content' => "Error: Gemini API key is not configured in .env (GEMINI_API_KEY)."
            ]);
        }

        $contents = [];
        foreach ($request->input('messages') as $msg) {
            $role = $msg['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['content']]
                ]
            ];
        }

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $courseContext]
                ]
            ],
            'contents' => $contents,
        ];

        try {
            $response = Http::timeout(15)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiText = $data['candidates'][0]['content']['parts'][0]['text'];
                    return response()->json([
                        'role' => 'assistant',
                        'content' => $aiText
                    ]);
                }
            }
            return response()->json([
                'role' => 'assistant',
                'content' => "Sorry, I received an unexpected response format from the AI server."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'role' => 'assistant',
                'content' => "Sorry, an error occurred while processing your request."
            ]);
        }
    }
}




