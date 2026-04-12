<section class="section class bg-dark has-bg-image" id="class" aria-label="class"
    style="background-image: url('{{ asset('fitlife-assets/images/classes-bg.png') }}')">
    <div class="container">

        <p class="section-subtitle">Learning Paths</p>

        <h2 class="h2 section-title text-center">SkillUp pathways for every focus</h2>

        <ul class="class-list has-scrollbar">
            @foreach ($classes as $class)
                <li class="scrollbar-item">
                    <div class="class-card">

                        <figure class="card-banner img-holder" style="--width: 416; --height: 240;">
                            <img src="{{ asset('fitlife-assets/images/' . ($class['image'] ?? 'class-1.jpg')) }}" width="416"
                                height="240" loading="lazy" alt="{{ $class['title'] }}" class="img-cover">
                        </figure>

                        <div class="card-content">

                            <div class="title-wrapper">
                                <img src="{{ asset('fitlife-assets/images/' . ($class['icon'] ?? 'class-icon-1.png')) }}"
                                    width="52" height="52" aria-hidden="true" alt="" class="title-icon">

                                <h3 class="h3">
                                    <a href="#" class="card-title">{{ $class['title'] }}</a>
                                </h3>
                            </div>

                            <p class="card-text">
                                {{ $class['description'] }}
                            </p>

                            <div class="card-progress">

                                <div class="progress-wrapper">
                                    <p class="progress-label">{{ $class['statusLabel'] ?? 'Path confidence' }}</p>

                                    <span class="progress-value">{{ (int) ($class['progress'] ?? 0) }}%</span>
                                </div>

                                <div class="progress-bg">
                                    <div class="progress-bar" style="width: {{ max(0, min(100, (int) ($class['progress'] ?? 0))) }}%">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </li>
            @endforeach
        </ul>

    </div>
</section>
