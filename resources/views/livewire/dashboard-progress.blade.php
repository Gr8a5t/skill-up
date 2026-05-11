<div wire:poll.10s="loadData">
    <div class="left-panel" style="min-width: 0;">
        <div class="hero-banner">
            <div class="hero-stars"></div>
            <div class="hero-label">Online Course</div>
            <h1 class="hero-title">Sharpen Your Skills with Professional Online Courses</h1>
            <a href="{{ route('courses') }}" class="hero-btn">Join Now <span wire:ignore><ion-icon name="chevron-forward-outline"></ion-icon></span></a>
        </div>

        <div class="metric-row">
            <div class="metric-card">
                <div class="m-icon m-icon-1" wire:ignore><ion-icon name="albums-outline"></ion-icon></div>
                <div class="m-details">
                    <p>Courses In Progress</p>
                    <h4>{{ $stats['courses_started'] }}</h4>
                </div>
            </div>
            <div class="metric-card">
                <div class="m-icon m-icon-2" wire:ignore><ion-icon name="checkmark-done-circle-outline"></ion-icon></div>
                <div class="m-details">
                    <p>Videos Completed</p>
                    <h4>{{ $stats['videos_completed'] }}</h4>
                </div>
            </div>
            <div class="metric-card">
                <div class="m-icon m-icon-3" wire:ignore><ion-icon name="trending-up-outline"></ion-icon></div>
                <div class="m-details">
                    <p>Overall Progress</p>
                    <h4>{{ $stats['overall_pct'] }}%</h4>
                </div>
            </div>
        </div>

        <div class="section-hdr">
            <h2 class="section-title">Recommended Courses</h2>
            <div class="section-nav">
                <button class="s-nav-btn" id="crs-btn-prev" onclick="document.getElementById('crs-carousel').scrollBy({left: -300, behavior: 'smooth'})" wire:ignore><ion-icon name="chevron-back-outline"></ion-icon></button>
                <button class="s-nav-btn active" id="crs-btn-next" onclick="document.getElementById('crs-carousel').scrollBy({left: 300, behavior: 'smooth'})" wire:ignore><ion-icon name="chevron-forward-outline"></ion-icon></button>
            </div>
        </div>
        
        <div class="cw-grid" id="crs-carousel" wire:ignore>
            @forelse($continueWatching as $cw)
            <a href="{{ route('courses.learn', $cw['slug']) }}" class="cw-card" style="text-decoration:none; color:inherit;">
                <div class="cw-img" style="background-color: {{ $cw['color'] }}; display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="{{ $cw['icon'] }}" style="font-size: 5rem; color: rgba(0,0,0,0.6);"></ion-icon>
                </div>
                <div class="cw-body">
                    <span class="cw-badge cw-badge-fe" style="margin-bottom: 14px;">{{ $cw['category'] }}</span>
                    <h3 class="cw-title" style="margin-bottom: 4px;">{{ \Str::limit($cw['title'], 48) }}</h3>
                </div>
            </a>
            @empty
            <p style="color:var(--text-mut); font-size:1.3rem;">No courses available. <a href="{{ route('courses') }}">Browse courses →</a></p>
            @endforelse
        </div>


        <div class="section-hdr" style="margin-top: 40px;">
            <h2 class="section-title">Your Lesson</h2>
            <a href="#" style="color:var(--brand-primary); font-weight:700; font-size:1.3rem; text-decoration:none;">See all</a>
        </div>

        <table class="lesson-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                <tr>
                    <td data-label="Course">
                        <div class="tbl-mentor">
                            <div class="cw-mentor-ava" style="width:36px; height:36px; font-size:1.2rem;">{{ strtoupper(substr($lesson['category'], 0, 1)) }}</div>
                            <div class="tbl-m-info">
                                <h5>{{ \Str::limit($lesson['title'], 30) }}</h5>
                                <p>{{ $lesson['updated'] ? \Carbon\Carbon::parse($lesson['updated'])->diffForHumans() : '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td data-label="Category">
                        <span class="tbl-badge" wire:ignore><ion-icon name="color-filter-outline"></ion-icon> {{ $lesson['category'] }}</span>
                    </td>
                    <td data-label="Progress" style="font-weight: 700; color: var(--brand-primary);">{{ $lesson['progress'] }}%</td>
                    <td data-label="Action">
                        <a href="{{ route('courses.learn', $lesson['course_slug']) }}?v={{ $lesson['video_id'] }}" class="action-arrow" wire:ignore><ion-icon name="arrow-forward-outline"></ion-icon></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; color:var(--text-mut); padding:30px;">No lessons started yet. <a href="{{ route('courses') }}">Start your first course →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Right Column -->
    <div class="right-panel" style="min-width: 0;">
        <div class="stat-widget">
            <div class="section-hdr" style="margin-bottom: 24px;">
                <h2 class="section-title">Statistic</h2>
                <span wire:ignore><ion-icon name="ellipsis-vertical" style="color:var(--text-mut); font-size:1.8rem;"></ion-icon></span>
            </div>
            
            <a href="{{ route('profile.show', auth()->user()) }}" class="stat-radial" style="display:flex; text-decoration:none; background: conic-gradient(var(--brand-primary) {{ $stats['overall_pct'] }}%, #f0f0f0 0);">
                <div class="stat-inner">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.rawurlencode(auth()->user()->name).'&background=f0ebff&color=8e54e9&rounded=true&size=100' }}" alt="Avatar" style="width: 100%; height: 100%; object-fit:cover;">
                </div>
                <div class="stat-val">{{ $stats['overall_pct'] }}%</div>
            </a>
            
            <div class="stat-greeting">Good Morning {{ explode(' ', auth()->user()->name)[0] }} 🔥</div>
            <div class="stat-sub">Continue your learning to achieve your target!</div>
            
            <div class="heatmap-wrap" id="activity-heatmap">
                <div class="heatmap-title">
                    Learning Activity
                    <span id="heatmap-streak"></span>
                </div>

                @php
                    $dates   = array_keys($activityMap);
                    $counts  = array_values($activityMap);
                    $columns = array_chunk(array_map(null, $dates, $counts), 7);
                @endphp

                <div class="heatmap-outer" wire:ignore>
                    <div class="heatmap-day-labels">
                        <div class="heatmap-day-label">Mon</div>
                        <div class="heatmap-day-label"></div>
                        <div class="heatmap-day-label">Wed</div>
                        <div class="heatmap-day-label"></div>
                        <div class="heatmap-day-label">Fri</div>
                        <div class="heatmap-day-label"></div>
                        <div class="heatmap-day-label"></div>
                    </div>
                    <div class="heatmap-right">
                        <div class="heatmap-month-row" id="heatmap-months"></div>
                        <div class="heatmap-grid" id="heatmap-grid">
                            @foreach($columns as $colIdx => $col)
                            <div class="heatmap-col" data-col="{{ $colIdx }}">
                                @foreach($col as [$date, $count])
                                @php
                                    $level = 0;
                                    if ($count >= 1) $level = 1;
                                    if ($count >= 3) $level = 2;
                                    if ($count >= 5) $level = 3;
                                    if ($count >= 8) $level = 4;
                                @endphp
                                <div class="heatmap-cell"
                                     data-level="{{ $level }}"
                                     data-date="{{ $date }}"
                                     data-count="{{ $count }}"
                                     title="{{ $count }} {{ $count == 1 ? 'activity' : 'activities' }} on {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}"></div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="heatmap-legend">
                    Less
                    <span class="legend-cell" style="background:#ede9f7"></span>
                    <span class="legend-cell" style="background:#c4b0f5"></span>
                    <span class="legend-cell" style="background:#9b77ee"></span>
                    <span class="legend-cell" style="background:#7340e0"></span>
                    <span class="legend-cell" style="background:#4a00c8"></span>
                    More
                </div>
            </div>
        </div>

        <div class="mentor-widget" wire:ignore>
            <div class="section-hdr">
                <h2 class="section-title">Close Friends</h2>
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
                    <button class="m-follow-btn"><ion-icon name="person-add-outline"></ion-icon> <span>Follow</span></button>
                </div>
                @endforeach
            </div>
            
            <a href="#" class="btn-full">See All</a>
        </div>
    </div>
</div>
