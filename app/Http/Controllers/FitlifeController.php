<?php

namespace App\Http\Controllers;

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

        $courses = $this->loadCourseraCourses(30);

        return view('fitlife.home', compact('classes', 'blogs', 'courses'));
    }

    public function about()
    {
        return view('fitlife.about');
    }

    private function getWebDevCourses()
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

    public function courses()
    {
        $courses = $this->getWebDevCourses();

        return view('fitlife.courses', compact('courses'));
    }

    public function courseLearn(string $slug)
    {
        $youtubeKey = config('services.youtube.key');
        
        $allCourses = $this->getWebDevCourses();
        $courseData = collect($allCourses)->firstWhere('slug', $slug) ?? $allCourses[5];
        
        $playlistId = $courseData['playlist_id'];
        
        $items = [];
        $apiError = null;

        if (empty($youtubeKey)) {
            $apiError = 'YOUTUBE_API_KEY is not configured on this server.';
            \Illuminate\Support\Facades\Log::error('[CourseLearn] ' . $apiError);
        } else {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(8)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                    'part'       => 'snippet',
                    'playlistId' => $playlistId,
                    'maxResults' => 20,
                    'key'        => $youtubeKey,
                ]);

                $json = $response->json();

                if (isset($json['error'])) {
                    $apiError = $json['error']['message'] ?? 'Unknown YouTube API error';
                    \Illuminate\Support\Facades\Log::error('[CourseLearn] YouTube API error: ' . $apiError);
                } else {
                    $items = $json['items'] ?? [];
                }
            } catch (\Exception $e) {
                $apiError = $e->getMessage();
                \Illuminate\Support\Facades\Log::error('[CourseLearn] HTTP exception: ' . $apiError);
            }
        }

        $course = [
            'title' => $courseData['title'],
            'category' => $courseData['category'],
            'level' => $courseData['level'],
            'lessons_count' => count($items),
            'duration' => 'Self Paced', // Duration from YouTube requires another API call, keeping simple
            'video_id' => $items[0]['snippet']['resourceId']['videoId'] ?? 'SqcY0GlETPk', 
            'recap' => 'In this course, we will build a modern application from scratch. You will learn about core syntax, state, context, and more.',
            'concepts' => [
                'Understanding ' . $courseData['title'] . ' fundamentals',
                'Managing state and side effects',
                'Applying styles and components',
                'Deploying to production'
            ]
        ];

        // Check active video from request or default to first
        $activeVideoId = request('v', $course['video_id']);
        $course['video_id'] = $activeVideoId; // The one currently playing

        $lessons = [];
        if (count($items) > 0) {
            // Fetch user progress for this course
            $progressRecords = [];
            if (auth()->check()) {
                $progressRecords = \App\Models\UserLessonProgress::where('course_slug', $slug)
                    ->where('user_id', auth()->id())
                    ->get()->keyBy('video_id');
            } else {
                $sessionId = session()->getId();
                $progressRecords = \App\Models\UserLessonProgress::where('course_slug', $slug)
                    ->where('session_id', $sessionId)
                    ->get()->keyBy('video_id');
            }

            foreach($items as $item) {
                $vidId = $item['snippet']['resourceId']['videoId'];
                $progressRec = $progressRecords[$vidId] ?? null;
                $pct = 0;
                if ($progressRec && $progressRec->total_seconds > 0) {
                    $pct = min(100, round(($progressRec->progress_seconds / $progressRec->total_seconds) * 100));
                }

                $lessons[] = [
                    'video_id' => $vidId,
                    'title' => $item['snippet']['title'],
                    'time' => 'Video', // 'time' removed as it requires extra API calls
                    'progress' => $pct,
                    'active' => ($vidId === $activeVideoId),
                ];
            }
        } else {
            // Fallback if API fails — use a sensible first video per course
            $fallbackVideos = [
                'html-basics'      => 'it1rTvBcfRg',
                'css-styling'      => 'wRNinF7YQqQ',
                'modern-javascript'=> 'W6NZfCO5SIk',
                'php-fundamentals' => 'OK_JCtrrv-c',
                'laravel-mastery'  => 'Rz6SMgKrSYE',
                'react-beginners'  => 'SqcY0GlETPk',
                'ai-agents-intro'  => 'VMj-3S1tku0',
            ];
            $fallbackId = $fallbackVideos[$slug] ?? 'SqcY0GlETPk';
            $lessons = [
                ['video_id' => $fallbackId, 'title' => $courseData['title'] . ' — Intro', 'time' => 'Video', 'progress' => 0, 'active' => true],
            ];
            $course['video_id'] = $fallbackId;
            $activeVideoId = $fallbackId;
        }


        return view('fitlife.course-learn', compact('course', 'lessons', 'slug'));
    }

    public function updateCourseProgress(\Illuminate\Http\Request $request)
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
        $courses = $this->loadCourseraCourses(120);
        $course = collect($courses)->firstWhere('slug', $slug);

        if (! $course) {
            abort(404);
        }

        $course = $this->loadCourseraCourseDetail($course);
        $content = $this->buildCourseContent($course);
        $learningPoints = $this->buildLearningPoints($course);
        $introLessons = $this->buildIntroLessons($course);

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

        return view('fitlife.paths', compact('paths'));
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
            'path_slug' => 'required|string',
            'module_index' => 'required|integer',
        ]);

        $sessionId = session()->getId();
        $userId = auth()->id();

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

    private function loadCourseraCourses(int $max = 30): array
    {
        $cacheKey = 'skillup.coursera.courses';
        $cached = $this->normalizeCourses(Cache::get($cacheKey, []));

        try {
            $fresh = $this->normalizeCourses($this->fetchCourseraCourses($max));

            if (!empty($fresh)) {
                Cache::put($cacheKey, $fresh, now()->addMinutes(30));
                return $fresh;
            }
        } catch (\Throwable $e) {
            Log::error('Failed to fetch Coursera courses: '.$e->getMessage());
        }

        return $cached;
    }

    private function fetchCourseraCourses(int $max): array
    {
        $target = max($max, 100);
        $courses = [];
        $usedSlugs = [];
        $cursor = null;
        $requests = 0;
        $maxRequests = 8;
        $levels = ['Junior', 'Medium', 'Advance'];
        $predefinedTags = [
            ['UX design', 'UI design'],
            ['Design psychology', 'Visual design'],
            ['Minimal UI design', 'Visual design'],
            ['Interface design', 'UX law'],
            ['Behavior design', 'UX design']
        ];
        $progressOpts = [0, 0, 12, 45, 60, 72, 80];
        $bannerColors = ['#e6eee0', '#e0e9f8', '#ffecd1', '#e8e2d4', '#f5e3e8', '#e8ecf1'];
        $icons = ['folder-open-outline', 'brush-outline', 'color-palette-outline', 'cube-outline', 'layers-outline', 'desktop-outline'];

        while (count($courses) < $target && $requests < $maxRequests) {
            $query = [
                'fields' => 'name,description,photoUrl,primaryLanguages,partnerIds,slug,workload,domainTypes,subtitleLanguages,instructorIds',
                'limit' => 100,
            ];

            if (!empty($cursor)) {
                $query['start'] = $cursor;
            }

            $response = Http::timeout(15)->get('https://api.coursera.org/api/courses.v1', $query);

            if (! $response->successful()) {
                break;
            }

            $data = $response->json();
            $elements = $data['elements'] ?? [];

            if (empty($elements)) {
                break;
            }

            foreach ($elements as $course) {
                $primaryLangs = array_map(fn ($lang) => strtolower($lang), $course['primaryLanguages'] ?? []);

                if (!in_array('en', $primaryLangs, true) && !in_array('en-us', $primaryLangs, true)) {
                    continue;
                }

                if (count($courses) >= $target) {
                    break;
                }

                $baseSlug = Str::slug($course['slug'] ?? $course['name'] ?? 'skillup-pick');
                $baseSlug = $baseSlug !== '' ? $baseSlug : 'skillup-pick';
                $slug = $baseSlug;
                $suffix = 2;

                while (isset($usedSlugs[$slug])) {
                    $slug = $baseSlug.'-'.$suffix;
                    $suffix++;
                }

                $usedSlugs[$slug] = true;
                $domainTags = $this->extractTagsFromDomainTypes($course['domainTypes'] ?? []);
                $workload = trim((string) ($course['workload'] ?? ''));
                $estimatedLessons = $this->estimateLessonsFromWorkload($workload, rand(5, 25));
                $level = $this->deriveLevelFromWorkload($workload) ?? $levels[array_rand($levels)];

                $courses[] = [
                    'id' => (string) ($course['id'] ?? ''),
                    'slug' => $slug,
                    'title' => $course['name'] ?? 'SkillUp pick',
                    'description' => trim($course['description'] ?? 'A practical course to keep improving.'),
                    'excerpt' => Str::limit(trim($course['description'] ?? 'A practical course to keep improving.'), 120),
                    'image' => $course['photoUrl'] ?? asset('fitlife-assets/images/hero-banner.png'),
                    'coursera_link' => !empty($course['slug']) ? 'https://www.coursera.org/learn/'.$course['slug'] : 'https://www.coursera.org',
                    'language' => implode(', ', $course['primaryLanguages'] ?? []),
                    'workload' => $workload,
                    'lessons' => $estimatedLessons,
                    'tags' => !empty($domainTags) ? $domainTags : $predefinedTags[array_rand($predefinedTags)],
                    'level' => $level,
                    'progress' => $progressOpts[array_rand($progressOpts)],
                    'banner_color' => $bannerColors[array_rand($bannerColors)],
                    'icon' => $icons[array_rand($icons)],
                    'partner_ids' => array_values(array_map('strval', $course['partnerIds'] ?? [])),
                    'instructor_ids' => array_values(array_map('strval', $course['instructorIds'] ?? [])),
                    'domain_types' => is_array($course['domainTypes'] ?? null) ? $course['domainTypes'] : [],
                    'subtitle_languages' => array_values(array_map('strval', $course['subtitleLanguages'] ?? [])),
                ];
            }

            $nextCursor = (string) ($data['paging']['next'] ?? '');
            $requests++;

            if ($nextCursor === '' || $nextCursor === (string) $cursor) {
                break;
            }

            $cursor = $nextCursor;
        }

        return $courses;
    }

    private function normalizeCourses(array $courses): array
    {
        $normalized = [];
        $usedSlugs = [];

        foreach ($courses as $course) {
            if (!is_array($course)) {
                continue;
            }

            $title = trim((string) ($course['title'] ?? 'SkillUp pick'));
            $description = trim((string) ($course['description'] ?? $course['excerpt'] ?? 'A practical course to keep improving.'));
            $baseSlug = Str::slug((string) ($course['slug'] ?? $title));
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'skillup-pick';
            $slug = $baseSlug;
            $suffix = 2;

            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }

            $usedSlugs[$slug] = true;
            $domainTypes = is_array($course['domain_types'] ?? null)
                ? $course['domain_types']
                : (is_array($course['domainTypes'] ?? null) ? $course['domainTypes'] : []);
            $workload = trim((string) ($course['workload'] ?? ''));
            $tags = is_array($course['tags'] ?? null) ? $course['tags'] : $this->extractTagsFromDomainTypes($domainTypes);
            $fallbackLessons = (int) ($course['lessons'] ?? 0);
            $estimatedLessons = $this->estimateLessonsFromWorkload($workload, $fallbackLessons > 0 ? $fallbackLessons : 8);
            $level = trim((string) ($course['level'] ?? ''));
            $level = $level !== '' ? $level : ($this->deriveLevelFromWorkload($workload) ?? 'Medium');

            $normalized[] = array_merge($course, [
                'id' => (string) ($course['id'] ?? ''),
                'slug' => $slug,
                'title' => $title,
                'description' => $description,
                'excerpt' => Str::limit($description, 120),
                'coursera_link' => (string) ($course['coursera_link'] ?? $course['link'] ?? 'https://www.coursera.org'),
                'workload' => $workload,
                'tags' => $tags,
                'language' => (string) ($course['language'] ?? ''),
                'lessons' => $estimatedLessons,
                'level' => $level,
                'partner_ids' => array_values(array_map('strval', $course['partner_ids'] ?? $course['partnerIds'] ?? [])),
                'instructor_ids' => array_values(array_map('strval', $course['instructor_ids'] ?? $course['instructorIds'] ?? [])),
                'domain_types' => $domainTypes,
                'subtitle_languages' => array_values(array_map('strval', $course['subtitle_languages'] ?? $course['subtitleLanguages'] ?? [])),
            ]);
        }

        return $normalized;
    }

    private function loadCourseraCourseDetail(array $course): array
    {
        $slug = trim((string) ($course['slug'] ?? ''));

        if ($slug === '') {
            return $course;
        }

        $cacheKey = 'skillup.coursera.course.'.$slug;
        $cached = Cache::get($cacheKey, []);

        if (is_array($cached) && !empty($cached)) {
            $normalized = $this->normalizeCourses([array_merge($course, $cached)]);

            return $normalized[0] ?? $course;
        }

        try {
            $fresh = $this->fetchCourseraCourseDetail($slug, $course);

            if (!empty($fresh)) {
                Cache::put($cacheKey, $fresh, now()->addMinutes(30));
                $normalized = $this->normalizeCourses([array_merge($course, $fresh)]);

                return $normalized[0] ?? array_merge($course, $fresh);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to fetch Coursera course detail: '.$e->getMessage());
        }

        return $course;
    }

    private function fetchCourseraCourseDetail(string $slug, array $fallbackCourse = []): array
    {
        $response = Http::timeout(15)
            ->get('https://api.coursera.org/api/courses.v1', [
                'q' => 'slug',
                'slug' => $slug,
                'fields' => 'name,description,photoUrl,primaryLanguages,partnerIds,slug,workload,domainTypes,subtitleLanguages,instructorIds',
            ]);

        if (! $response->successful()) {
            return [];
        }

        $apiCourse = $response->json('elements.0');

        if (!is_array($apiCourse)) {
            return [];
        }

        $domainTypes = is_array($apiCourse['domainTypes'] ?? null) ? $apiCourse['domainTypes'] : [];
        $workload = trim((string) ($apiCourse['workload'] ?? ''));
        $partnerIds = array_values(array_map('strval', $apiCourse['partnerIds'] ?? []));
        $instructorIds = array_values(array_map('strval', $apiCourse['instructorIds'] ?? []));
        $tags = $this->extractTagsFromDomainTypes($domainTypes);

        $detail = [
            'id' => (string) ($apiCourse['id'] ?? ''),
            'slug' => (string) ($apiCourse['slug'] ?? $slug),
            'title' => trim((string) ($apiCourse['name'] ?? ($fallbackCourse['title'] ?? 'SkillUp pick'))),
            'description' => trim((string) ($apiCourse['description'] ?? ($fallbackCourse['description'] ?? 'A practical course to keep improving.'))),
            'image' => (string) ($apiCourse['photoUrl'] ?? ($fallbackCourse['image'] ?? '')),
            'coursera_link' => !empty($apiCourse['slug']) ? 'https://www.coursera.org/learn/'.$apiCourse['slug'] : ($fallbackCourse['coursera_link'] ?? 'https://www.coursera.org'),
            'language' => implode(', ', $apiCourse['primaryLanguages'] ?? []),
            'workload' => $workload,
            'tags' => !empty($tags) ? $tags : ($fallbackCourse['tags'] ?? []),
            'lessons' => $this->estimateLessonsFromWorkload($workload, (int) ($fallbackCourse['lessons'] ?? 8)),
            'level' => $this->deriveLevelFromWorkload($workload) ?? ($fallbackCourse['level'] ?? 'Medium'),
            'partner_ids' => $partnerIds,
            'instructor_ids' => $instructorIds,
            'domain_types' => $domainTypes,
            'subtitle_languages' => array_values(array_map('strval', $apiCourse['subtitleLanguages'] ?? [])),
        ];

        $partner = $this->fetchCourseraPartner($partnerIds);
        $instructor = $this->fetchCourseraInstructor($instructorIds);

        return array_merge($detail, $this->buildAuthorMeta($instructor, $partner, $detail['title']));
    }

    private function fetchCourseraPartner(array $partnerIds): array
    {
        if (empty($partnerIds)) {
            return [];
        }

        $response = Http::timeout(15)
            ->get('https://api.coursera.org/api/partners.v1', [
                'ids' => implode(',', $partnerIds),
                'fields' => 'name,description,logo,shortName',
            ]);

        if (! $response->successful()) {
            return [];
        }

        $partner = $response->json('elements.0');

        return is_array($partner) ? $partner : [];
    }

    private function fetchCourseraInstructor(array $instructorIds): array
    {
        if (empty($instructorIds)) {
            return [];
        }

        $response = Http::timeout(15)
            ->get('https://api.coursera.org/api/instructors.v1', [
                'ids' => implode(',', $instructorIds),
                'fields' => 'fullName,title,bio,photo',
            ]);

        if (! $response->successful()) {
            return [];
        }

        $instructor = $response->json('elements.0');

        return is_array($instructor) ? $instructor : [];
    }

    private function buildAuthorMeta(array $instructor, array $partner, string $title): array
    {
        $authorName = trim((string) ($instructor['fullName'] ?? ''));
        $authorName = $authorName !== '' ? $authorName : trim((string) ($partner['name'] ?? 'Coursera Instructor'));

        $authorRole = trim((string) ($instructor['title'] ?? ''));
        $authorRole = $authorRole !== '' ? $authorRole : trim((string) ($partner['name'] ?? 'Course Partner'));

        $authorBio = trim((string) ($instructor['bio'] ?? $partner['description'] ?? 'Learn from top instructors and institutions on Coursera.'));
        $authorImage = trim((string) ($instructor['photo'] ?? $partner['logo'] ?? ''));

        if ($authorImage === '') {
            $authorImage = 'https://ui-avatars.com/api/?name='.rawurlencode($authorName).'&background=0D8ABC&color=fff&rounded=true';
        }

        $seed = (int) (abs(crc32($title.$authorName)) % 10);
        $rating = 4 + ($seed / 10);
        $reviews = 80 + ((int) (abs(crc32($title)) % 420));

        return [
            'author_name' => $authorName,
            'author_role' => $authorRole,
            'author_bio' => Str::limit($authorBio, 320),
            'author_image' => $authorImage,
            'rating' => number_format($rating, 1),
            'reviews_count' => $reviews,
        ];
    }

    private function buildCourseContent(array $course): array
    {
        $lessons = max((int) ($course['lessons'] ?? 8), 6);
        $totalMinutes = $this->workloadToMinutes((string) ($course['workload'] ?? ''));
        $totalMinutes = $totalMinutes > 0 ? $totalMinutes : max($lessons * 7, 45);
        $topicA = $course['tags'][0] ?? 'Core Skills';
        $topicB = $course['tags'][1] ?? 'Applied Practice';
        $sections = [
            'Introduction',
            $topicA.' Fundamentals',
            'Hands-on '.$topicA,
            $topicB.' Workflow',
            'Project Application',
            'Summary & Next Steps',
        ];
        $weights = [0.12, 0.2, 0.2, 0.18, 0.18, 0.12];

        $items = [];
        $remaining = $lessons;
        $remainingMinutes = $totalMinutes;

        foreach ($sections as $index => $title) {
            $left = count($sections) - $index;
            $sectionLessons = max(1, (int) floor($remaining / $left));
            $remaining -= $sectionLessons;
            $minutes = $left === 1
                ? max(4, $remainingMinutes)
                : max(4, (int) round($totalMinutes * ($weights[$index] ?? 0.15)));
            $remainingMinutes -= $minutes;

            $items[] = [
                'title' => $title,
                'lessons' => $sectionLessons,
                'duration' => $this->formatDuration($minutes),
            ];
        }

        return $items;
    }

    private function buildLearningPoints(array $course): array
    {
        $points = [];
        $tags = array_values(array_filter($course['tags'] ?? []));

        foreach (array_slice($tags, 0, 4) as $tag) {
            $points[] = 'Build confidence with '.$tag.' concepts and practical workflows.';
        }

        $points[] = 'Apply '.$course['title'].' lessons through guided exercises and project scenarios.';
        $points[] = 'Use repeatable frameworks you can transfer into portfolio or work tasks.';

        $fallbacks = [
            'Strengthen your foundations with structured, step-by-step guidance.',
            'Practice with realistic use-cases to improve speed and confidence.',
            'Translate theory into practical outputs you can reuse in projects.',
            'Build an end-to-end workflow you can adapt to your own goals.',
        ];

        foreach ($fallbacks as $fallback) {
            if (count($points) >= 6) {
                break;
            }

            $points[] = $fallback;
        }

        return array_slice(array_values(array_unique($points)), 0, 6);
    }

    private function buildIntroLessons(array $course): array
    {
        $topicA = $course['tags'][0] ?? 'Core Concepts';
        $topicB = $course['tags'][1] ?? 'Workflow';

        return [
            ['title' => 'Welcome and course roadmap', 'duration' => '2 min'],
            ['title' => 'What is '.$topicA.'?', 'duration' => '5 min'],
            ['title' => 'Understanding '.$topicB, 'duration' => '8 min'],
            ['title' => 'Guided setup and first exercise', 'duration' => '4 min'],
        ];
    }

    private function extractTagsFromDomainTypes(array $domainTypes): array
    {
        $tags = [];

        foreach ($domainTypes as $domainType) {
            if (!is_array($domainType)) {
                continue;
            }

            foreach (['subdomainId', 'domainId'] as $key) {
                $value = trim((string) ($domainType[$key] ?? ''));

                if ($value === '') {
                    continue;
                }

                $tags[] = Str::of($value)->replace('-', ' ')->title()->toString();
            }
        }

        return array_values(array_slice(array_unique($tags), 0, 3));
    }

    private function estimateLessonsFromWorkload(string $workload, int $fallback = 8): int
    {
        $minutes = $this->workloadToMinutes($workload);

        if ($minutes <= 0) {
            return max($fallback, 5);
        }

        return max(5, min(36, (int) round($minutes / 8)));
    }

    private function deriveLevelFromWorkload(string $workload): ?string
    {
        $minutes = $this->workloadToMinutes($workload);

        if ($minutes <= 0) {
            return null;
        }

        if ($minutes <= 180) {
            return 'Junior';
        }

        if ($minutes <= 480) {
            return 'Medium';
        }

        return 'Advance';
    }

    private function workloadToMinutes(string $workload): int
    {
        $value = strtolower(trim($workload));

        if ($value === '') {
            return 0;
        }

        $minutes = 0;

        if (preg_match('/(\d+)\s*hour/', $value, $hourMatch)) {
            $minutes += ((int) $hourMatch[1]) * 60;
        }

        if (preg_match('/(\d+)\s*minute/', $value, $minuteMatch)) {
            $minutes += (int) $minuteMatch[1];
        }

        if ($minutes === 0 && preg_match('/(\d+)\s*week/', $value, $weekMatch)) {
            $minutes += ((int) $weekMatch[1]) * 120;
        }

        if ($minutes === 0 && preg_match('/(\d+)\s*day/', $value, $dayMatch)) {
            $minutes += ((int) $dayMatch[1]) * 45;
        }

        return $minutes;
    }

    private function formatDuration(int $minutes): string
    {
        if ($minutes >= 60) {
            $hours = intdiv($minutes, 60);
            $remainder = $minutes % 60;

            return $remainder > 0 ? $hours.'h '.$remainder.'min' : $hours.'h';
        }

        return $minutes.' min';
    }

    public function dashboard()
    {
        // Provide dummy lists to match the mockup
        $continueWatching = [
            [
                'title' => 'Beginner\'s Guide to Becoming a Professional Front-End Developer',
                'category' => 'FRONT END',
                'mentor' => 'Leonardo samsul',
                'image' => asset('fitlife-assets/images/blog-1.jpg')
            ],
            [
                'title' => 'Optimizing User Experience with the Best UI/UX Design',
                'category' => 'UI/UX DESIGN',
                'mentor' => 'Bayu Salto',
                'image' => asset('fitlife-assets/images/blog-2.jpg')
            ],
            [
                'title' => 'Reviving and Refresh Company Image',
                'category' => 'BRANDING',
                'mentor' => 'Padhang Satrio',
                'image' => asset('fitlife-assets/images/blog-3.jpg')
            ],
            [
                'title' => 'Mastering App Marketing & Growth Hacking',
                'category' => 'MARKETING',
                'mentor' => 'Zakir Horizontal',
                'image' => asset('fitlife-assets/images/blog-4.jpg')
            ],
        ];

        $lessons = [
            [
                'mentor' => 'Padhang Satrio',
                'date' => '2/16/2004',
                'type' => 'UI/UX DESIGN',
                'desc' => 'Understand Of UI/UX Design'
            ],
            [
                'mentor' => 'Leonardo samsul',
                'date' => '3/10/2004',
                'type' => 'FRONT END',
                'desc' => 'Structure with HTML5'
            ],
            [
                'mentor' => 'Bayu Salto',
                'date' => '4/05/2004',
                'type' => 'BRANDING',
                'desc' => 'Brand Identity Assets'
            ],
        ];

        $mentors = [
            ['name' => 'Padhang Satrio', 'role' => 'Mentor', 'avatar' => 'PS'],
            ['name' => 'Zakir Horizontal', 'role' => 'Mentor', 'avatar' => 'ZH'],
            ['name' => 'Leonardo samsul', 'role' => 'Mentor', 'avatar' => 'LS'],
        ];

        $conversations = [
            [
                'name' => 'Ankit Chaudhary',
                'time' => '1h',
                'message' => 'your channel will be rock crazy if you can implement th...',
                'avatar' => 'https://i.pravatar.cc/150?u=ankit',
                'unread' => false,
            ],
            [
                'name' => 'Linden Clarkson (1)',
                'time' => '5h',
                'message' => 'Hey Lux, welcome to Amplify Views! To get the most ou...',
                'avatar' => 'https://i.pravatar.cc/150?u=linden',
                'unread' => true,
            ],
            [
                'name' => 'Owen Popelycher',
                'time' => '8h',
                'message' => 'Hey Lux?',
                'avatar' => 'https://i.pravatar.cc/150?u=owen',
                'unread' => false,
            ],
            [
                'name' => 'Erin Pope',
                'time' => '15h',
                'message' => 'Hey Lux, welcome to the Content Academy! I work with ...',
                'avatar' => 'https://i.pravatar.cc/150?u=erin',
                'unread' => false,
            ],
        ];

        // Activity heatmap — count progress updates per day over the last 365 days
        $userId    = auth()->id();
        $sessionId = session()->getId();

        $activityRaw = \App\Models\UserLessonProgress::where(function ($q) use ($userId, $sessionId) {
                if ($userId) $q->where('user_id', $userId);
                else         $q->where('session_id', $sessionId);
            })
            ->where('updated_at', '>=', now()->subDays(365))
            ->selectRaw('DATE(updated_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Build a full 365-day map (today → 364 days ago) keyed by Y-m-d
        $activityMap = [];
        for ($i = 364; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityMap[$date] = $activityRaw[$date] ?? 0;
        }

        return view('fitlife.user-dashboard', compact('continueWatching', 'lessons', 'mentors', 'conversations', 'activityMap'));
    }

    public function chats()
    {
        $conversations = [
            [
                'name' => 'Ankit Chaudhary',
                'time' => '1h',
                'message' => 'your channel will be rock crazy if you can implement th...',
                'avatar' => 'https://i.pravatar.cc/150?u=ankit',
                'unread' => false,
            ],
            [
                'name' => 'Linden Clarkson (1)',
                'time' => '5h',
                'message' => 'Hey Lux, welcome to Amplify Views! To get the most ou...',
                'avatar' => 'https://i.pravatar.cc/150?u=linden',
                'unread' => true,
            ],
            [
                'name' => 'Owen Popelycher',
                'time' => '8h',
                'message' => 'Hey Lux?',
                'avatar' => 'https://i.pravatar.cc/150?u=owen',
                'unread' => false,
            ],
            [
                'name' => 'Erin Pope',
                'time' => '15h',
                'message' => 'Hey Lux, welcome to the Content Academy! I work with ...',
                'avatar' => 'https://i.pravatar.cc/150?u=erin',
                'unread' => false,
            ],
            [
                'name' => 'Money Maxxing',
                'time' => '16h',
                'message' => 'Yoo Whatsup Lux, welcome! Go check this out now http...',
                'avatar' => 'https://i.pravatar.cc/150?u=money',
                'unread' => false,
            ],
        ];

        $activeMessages = [
            ['sender' => 'Ankit Chaudhary', 'time' => '1:30 PM', 'text' => 'Hey! Have you seen the new design update?', 'is_me' => false],
            ['sender' => 'You', 'time' => '1:32 PM', 'text' => 'Not yet, just jumping in now.', 'is_me' => true],
            ['sender' => 'Ankit Chaudhary', 'time' => '1:35 PM', 'text' => 'It looks great. Your channel will be rock crazy if you can implement this.', 'is_me' => false],
        ];

        return view('fitlife.chats', compact('conversations', 'activeMessages'));
    }
}
