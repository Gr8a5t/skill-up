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

        return view('fitlife.home', compact('classes', 'blogs'));
    }

    public function about()
    {
        return view('fitlife.about');
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
        return view('fitlife.courses', compact('courses'));
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

        return view('fitlife.course-learn', compact('course', 'lessons', 'slug'));
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

        // Legacy compatibility for views that expect these variables
        $content = [];
        $learningPoints = $dbCourse?->key_concepts ?? ['Core fundamentals', 'Best practices', 'Hands-on projects'];
        $introLessons = [];

        return view('fitlife.course-detail', compact('course', 'content', 'learningPoints', 'introLessons'));
    }

    public function paths()
    {
        $paths = $this->skillUpPaths();

        // If we want to show real progress from DB
        $sessionId = session()->getId();
        $userId = auth()->id();

        foreach ($paths as &$path) {
            $completedCount = DB::table('user_path_progress')
                ->where('path_slug', $path['slug'])
                ->where(function($q) use ($userId, $sessionId) {
                    if ($userId) $q->where('user_id', $userId);
                    else $q->where('session_id', $sessionId);
                })
                ->count();

            $totalModules = count($path['modules']);
            if ($totalModules > 0) {
                $path['progress'] = round(($completedCount / $totalModules) * 100);
            }
        }

        return view('fitlife.paths', [
            'paths' => $paths,
            'noTopbar' => true,
            'noSidebar' => true
        ]);
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

        return view('fitlife.path-learn', compact('path', 'completedModules'));
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
        return view('fitlife.user-dashboard');
    }

    public function chats()
    {
        return view('fitlife.chats');
    }

    public function forum()
    {
        return view('fitlife.forum', ['noTopbar' => true, 'noSidebar' => true]);
    }
}
