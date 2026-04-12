<section class="section courses" aria-label="courses">
    <div class="container">
        
        <div class="courses-toolbar">
            <div class="courses-toolbar-left">
                <h2 class="h2 courses-toolbar-title">All Courses <span class="courses-count" data-courses-count>{{ count($courses) }}</span></h2>
            </div>
            <div class="courses-toolbar-right">
                <div class="toolbar-popover" data-filter-root>
                    <button class="btn-toolbar" type="button" data-filter-toggle><ion-icon name="options-outline"></ion-icon> Filter</button>
                    <div class="toolbar-menu" data-filter-menu hidden>
                        <label class="toolbar-label" for="filter-level">Level</label>
                        <select id="filter-level" class="toolbar-select" data-filter-level>
                            <option value="all">All levels</option>
                            <option value="junior">Junior</option>
                            <option value="medium">Medium</option>
                            <option value="advance">Advance</option>
                        </select>

                        <label class="toolbar-label" for="filter-status">Status</label>
                        <select id="filter-status" class="toolbar-select" data-filter-status>
                            <option value="all">All status</option>
                            <option value="not_started">Not started</option>
                            <option value="in_progress">In progress</option>
                            <option value="completed">Completed</option>
                        </select>

                        <button type="button" class="toolbar-reset-btn" data-filter-reset>Reset filters</button>
                    </div>
                </div>

                <div class="toolbar-popover" data-sort-root>
                    <button class="btn-toolbar" type="button" data-sort-toggle><ion-icon name="funnel-outline"></ion-icon> Sort by</button>
                    <div class="toolbar-menu" data-sort-menu hidden>
                        <label class="toolbar-label" for="sort-by">Order</label>
                        <select id="sort-by" class="toolbar-select" data-sort-select>
                            <option value="default">Default</option>
                            <option value="title_asc">Title A-Z</option>
                            <option value="title_desc">Title Z-A</option>
                            <option value="lessons_desc">Lessons high-low</option>
                            <option value="lessons_asc">Lessons low-high</option>
                            <option value="progress_desc">Progress high-low</option>
                            <option value="progress_asc">Progress low-high</option>
                        </select>
                    </div>
                </div>

                <div class="view-toggles">
                    <button class="btn-view"><ion-icon name="menu-outline"></ion-icon></button>
                    <button class="btn-view active"><ion-icon name="grid-outline"></ion-icon></button>
                </div>
            </div>
        </div>

        @if ($courses)
            <div class="courses-loading" data-courses-loading>
                <div class="spinner"></div>
                <p>Loading the latest SkillUp picks...</p>
            </div>
            <div class="courses-grid" data-courses-grid>
                @foreach ($courses as $course)
                    <article
                        class="course-card"
                        data-title="{{ strtolower($course['title'] ?? '') }}"
                        data-level="{{ strtolower($course['level'] ?? '') }}"
                        data-progress="{{ (int) ($course['progress'] ?? 0) }}"
                        data-lessons="{{ (int) ($course['lessons'] ?? 0) }}"
                        data-language="{{ strtolower($course['language'] ?? '') }}"
                        data-tags="{{ strtolower(implode(',', $course['tags'] ?? [])) }}"
                    >
                        <div class="course-card-banner" style="background-color: {{ $course['banner_color'] ?? '#dce4cd' }};">
                            <span class="course-lessons-badge">{{ $course['lessons'] ?? 0 }} lessons</span>
                            <div class="course-banner-icon-bg">
                                <ion-icon name="{{ $course['icon'] ?? 'folder-outline' }}" class="course-banner-icon"></ion-icon>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="course-tags">
                                @foreach($course['tags'] ?? [] as $tag)
                                    <span class="course-tag">{{ $tag }}</span>
                                @endforeach
                            </div>
                            <h3 class="h3 course-title" title="{{ $course['title'] }}">
                                {{ \Illuminate\Support\Str::limit($course['title'], 55) }}
                            </h3>
                            
                            <div class="course-footer">
                                <span class="course-level">Level: <strong>{{ $course['level'] ?? 'Beginner' }}</strong></span>
                                
                                <div class="course-progress-wrapper">
                                    @if(($course['progress'] ?? 0) > 0)
                                        <span class="progress-text">Progress:</span>
                                        <div class="progress-pie" style="--p:{{ $course['progress'] }}">
                                            <span>{{ $course['progress'] }}%</span>
                                        </div>
                                    @else
                                        <span class="course-not-started">Not Started</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('courses.show', $course['slug']) }}" class="course-overlay-link" aria-label="View course preview"></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="section-text text-center courses-empty" data-courses-empty hidden>No courses match the selected filter.</p>
            
            <div class="courses-pagination" data-courses-pagination>
                <div class="pagination-show">
                    <select class="pagination-select" data-pagination-size>
                        <option>16</option>
                        <option>32</option>
                        <option>48</option>
                    </select>
                    <span class="pagination-results" data-pagination-results>Results: 1 - 16 of {{ count($courses) }}</span>
                </div>
                <div class="pagination-controls" data-pagination-controls>
                    <button class="btn-page disabled" data-pagination-prev><ion-icon name="chevron-back-outline"></ion-icon> Previous</button>
                    <div class="pagination-pages" data-pagination-pages></div>
                    <button class="btn-page" data-pagination-next>Next <ion-icon name="chevron-forward-outline"></ion-icon></button>
                </div>
            </div>
            
        @else
            <p class="section-text text-center">Unable to load new courses at the moment. Please try again in a bit.</p>
        @endif
    </div>
</section>
