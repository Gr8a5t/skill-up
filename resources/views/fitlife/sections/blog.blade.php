<section class="section blog" id="blog" aria-label="blog">
    <div class="container">

        <p class="section-subtitle">SkillUp News</p>

        <h2 class="h2 section-title text-center">Fresh updates and mentor notes</h2>

        <ul class="blog-list has-scrollbar">
            @foreach ($blogs as $blog)
                <li class="scrollbar-item">
                    <div class="blog-card">

                        <div class="card-banner img-holder" style="--width: 440; --height: 270;">
                            <img src="{{ asset('fitlife-assets/images/' . $blog['image']) }}" width="440" height="270" loading="lazy"
                                alt="{{ $blog['alt'] }}" class="img-cover">

                            <time class="card-meta" datetime="{{ $blog['datetime'] }}">{{ $blog['date'] }}</time>
                        </div>

                        <div class="card-content">

                            <h3 class="h3">
                                <a href="#" class="card-title">{{ $blog['title'] }}</a>
                            </h3>

                            <p class="card-text">
                                {{ $blog['excerpt'] }}
                            </p>

                            <a href="#" class="btn-link has-before">Read More</a>

                        </div>

                    </div>
                </li>
            @endforeach
        </ul>

    </div>
</section>
