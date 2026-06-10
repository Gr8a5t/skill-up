<div class="forum-container" style="display: flex; gap: 30px; margin-top: 20px;">
    <!-- Main Feed -->
    <div class="forum-main" style="flex: 1; min-width: 0;">
        <!-- Filters -->
        <div class="forum-filters" style="display: flex; gap: 12px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none;">
            @foreach($topics as $topic)
                <button wire:click="setTab('{{ $topic }}')" 
                        class="filter-pill {{ $activeTab === $topic ? 'active' : '' }}"
                        style="padding: 10px 20px; border-radius: 20px; font-weight: 600; font-size: 1.3rem; border: none; cursor: pointer; white-space: nowrap; transition: 0.2s; {{ $activeTab === $topic ? 'background: var(--brand-primary); color: #fff;' : 'background: var(--bg-surface); color: var(--text-mut); border: 1px solid var(--border-color);' }}">
                    {{ $topic }}
                </button>
            @endforeach
        </div>

        <!-- Create Post -->
        <div class="create-post-card" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 30px;">
            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <div class="user-avatar" style="width: 48px; height: 48px; border-radius: 50%; background: #f0ebff; color: #8e54e9; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; overflow: hidden; font-size: 1.4rem;">
                    @if(auth()->check() && auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" style="width:100%; height:100%; object-fit:cover;" />
                    @elseif(auth()->check())
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @else
                        ?
                    @endif
                </div>
                <div style="flex: 1;">
                    <textarea wire:model="body" placeholder="What are you working on, {{ auth()->check() ? explode(' ', auth()->user()->name)[0] : 'Guest' }}?" style="width: 100%; border: none; background: transparent; font-size: 1.6rem; color: var(--text-main); outline: none; resize: none; min-height: 60px; margin-top: 8px; font-weight: 500;"></textarea>
                    @error('body') <span style="color: red; font-size: 1.2rem;">{{ $message }}</span> @enderror
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; gap: 16px; color: var(--text-mut); font-size: 1.6rem;">
                            <button style="background: none; border: none; cursor: pointer; color: inherit; display:flex; align-items:center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </button>
                            <button style="background: none; border: none; cursor: pointer; color: inherit; display:flex; align-items:center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                            </button>
                            <button style="background: none; border: none; cursor: pointer; color: inherit; display:flex; align-items:center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                            </button>
                            <button style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.2rem; font-weight: 600; display:flex; align-items:center; gap:6px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg> Add tags
                            </button>
                        </div>
                        <button wire:click="createPost" style="background: #1c1c1c; color: #fff; padding: 10px 24px; border-radius: 24px; font-size: 1.2rem; font-weight: bold; border: none; cursor: pointer;">Post</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suggested Topics -->
        <div style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 1.2rem; color: var(--text-mut); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Suggested Topics</h3>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-mut); cursor: pointer;"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div style="display: flex; gap: 20px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none;">
                <!-- Card 1 -->
                <div style="min-width: 320px; height: 180px; border-radius: 16px; background: #0f4c3a; color: #fff; padding: 20px; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="position: absolute; right: -20px; bottom: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; left: 40px; top: -30px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <h4 style="font-size: 1.4rem; font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 2.001c-3.313 0-6 2.686-6 6v3.013h-4v-3.013h4v3.013h4v-3.013c0-3.314 2.687-6 6-6zM8 11.014c-3.313 0-6 2.687-6 6s2.687 6 6 6 6-2.687 6-6v-3.013h-4v-2.987h-2z"/></svg> 
                            # Config Makeathon 
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: auto;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </h4>
                        <p style="font-size: 1.2rem; opacity: 0.9;">$100k in prizes for ideas built in Figma</p>
                    </div>
                    <div style="position: relative; z-index: 1; display: flex; gap: 16px;">
                        <div>
                            <div style="font-size: 1.4rem; font-weight: 700;">$100K</div>
                            <div style="font-size: 1.1rem; opacity: 0.8;">Prize</div>
                        </div>
                        <div>
                            <div style="font-size: 1.4rem; font-weight: 700;">10d</div>
                            <div style="font-size: 1.1rem; opacity: 0.8;">Left</div>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div style="min-width: 320px; height: 180px; border-radius: 16px; background: #2a2a2a; color: #fff; padding: 20px; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="position: relative; z-index: 1;">
                        <h4 style="font-size: 1.4rem; font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span style="font-size: 1rem; background: #fff; color: #000; padding: 2px 6px; border-radius: 4px;">Ta</span> # CapCut DesignStudio 
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: auto;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </h4>
                        <p style="font-size: 1.2rem; opacity: 0.9;">Submit your design work to join the challenge.</p>
                    </div>
                    <div style="position: relative; z-index: 1; display: flex; gap: 16px; align-items: flex-end;">
                        <div>
                            <div style="font-size: 1.4rem; font-weight: 700;">$7.5K</div>
                            <div style="font-size: 1.1rem; opacity: 0.8;">Prize</div>
                        </div>
                        <div>
                            <div style="font-size: 1.4rem; font-weight: 700;">2d ago</div>
                            <div style="font-size: 1.1rem; opacity: 0.8;">Ended</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feed -->
        <div class="forum-feed" style="display: flex; flex-direction: column; gap: 20px;">
            @forelse($posts as $post)
                <div class="post-card" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px;">
                    <div class="post-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div style="display: flex; gap: 14px; align-items: center;">
                            <div class="user-avatar" style="width: 48px; height: 48px; border-radius: 50%; background: #ffe4ef; color: #ff4aa0; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: bold; overflow: hidden;">
                                @if($post->user->avatar)
                                    <img src="{{ $post->user->avatar }}" style="width:100%; height:100%; object-fit:cover;" />
                                @else
                                    {{ substr($post->user->name, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <h4 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 2px;">{{ $post->user->name }}</h4>
                                <span style="font-size: 1.2rem; color: var(--text-mut);">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <button style="background: none; border: none; color: var(--text-mut); font-weight: 600; font-size: 1.3rem; cursor: pointer;">Follow</button>
                    </div>
                    
                    <div class="post-body" style="font-size: 1.5rem; line-height: 1.6; color: var(--text-main); margin-bottom: 16px;">
                        {{ $post->body }}
                    </div>

                    @if($post->tags)
                        <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                            @foreach($post->tags as $tag)
                                <span style="background: #f0f0f0; padding: 6px 12px; border-radius: 12px; font-size: 1.1rem; color: var(--text-mut); font-weight: 600;">#{{ strtolower(str_replace(' ', '', $tag)) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="post-actions" style="display: flex; gap: 24px; color: var(--text-mut); font-size: 1.4rem; border-top: 1px solid var(--border-color); padding-top: 16px;">
                        <button style="background: none; border: none; color: inherit; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg> 
                            {{ $post->likes_count }}
                        </button>
                        <button style="background: none; border: none; color: inherit; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> 
                            {{ $post->comments_count }}
                        </button>
                        <button style="background: none; border: none; color: inherit; cursor: pointer; margin-left: auto;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: var(--text-mut); font-size: 1.4rem;">
                    No posts yet. Be the first to share!
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="forum-sidebar" style="width: 320px; flex-shrink: 0; display: flex; flex-direction: column; gap: 24px;">
        <!-- Challenges -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 1.2rem; color: var(--text-mut); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Challenges</h3>
                <a href="#" style="font-size: 1.3rem; color: var(--brand-primary); text-decoration: none; font-weight: 600;">View all</a>
            </div>
            <div style="display: flex; gap: 14px; align-items: center;">
                <div style="width: 48px; height: 48px; border-radius: 10px; background: #1c1c1c; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path></svg>
                </div>
                <div>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 2px;">Config Makeathon</h4>
                    <p style="font-size: 1.2rem; color: var(--text-mut);">$100K • 10d left</p>
                </div>
            </div>
        </div>

        <!-- Trending Topics -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.2rem; color: var(--text-mut); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Trending</h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-mut);"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-mut);"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg> 
                        Claude
                    </h4>
                    <p style="font-size: 1.3rem; color: var(--text-mut); line-height: 1.5;">Claude has entered the design space. How are you using Claude Design?</p>
                </div>
                <div>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-mut);"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg> 
                        SkillUp University
                    </h4>
                    <p style="font-size: 1.3rem; color: var(--text-mut); line-height: 1.5;">Learn from expert creatives how to earn more using next-gen AI tools.</p>
                </div>
                <div>
                    <h4 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 6px;">#creativeaiflow</h4>
                    <p style="font-size: 1.3rem; color: var(--text-mut); line-height: 1.5;">Creative AI workflows are evolving. What tools do you use, and what are their strengths?</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .filter-pill::-webkit-scrollbar { display: none; }
    @media (max-width: 992px) {
        .forum-container { flex-direction: column; }
        .forum-sidebar { width: 100%; }
    }
</style>
