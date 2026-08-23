<?php
    $isEnglishMatches = @Helper::currentLanguage()->code === 'en';
    $selectedHomeMatches = \App\Models\Fixture::query()
        ->where('is_home', true)
        ->with(['league', 'season', 'homeTeam', 'awayTeam', 'round'])
        ->get();

    $homeMatchIsLive = static function ($match): bool {
        $stateCode = strtoupper((string) $match->state_code);
        return !$match->is_finished && ($match->minute || preg_match('/LIVE|INPLAY|BREAK|HT/', $stateCode));
    };

    $liveHomeMatches = $selectedHomeMatches->filter($homeMatchIsLive)->sortBy('starting_at');
    $upcomingHomeMatches = $selectedHomeMatches
        ->reject($homeMatchIsLive)
        ->where('is_finished', false)
        ->sortBy('starting_at');
    $finishedHomeMatches = $selectedHomeMatches
        ->where('is_finished', true)
        ->sortByDesc('starting_at');
    $homeMatches = $liveHomeMatches
        ->concat($upcomingHomeMatches)
        ->concat($finishedHomeMatches)
        ->take(9);
?>

@if($homeMatches->isNotEmpty())
    <section class="home-fixtures home-fixtures-v4" aria-labelledby="home-fixtures-title">
        <div class="container">
            <header class="home-fixtures-head">
                <div>
                    <span class="home-fixtures-eyebrow"><i></i>{{ __('frontend.homeMatchesEyebrow') }}</span>
                    <h2 id="home-fixtures-title">{{ __('frontend.homeMatchesTitle') }}</h2>
                    <p>{{ __('frontend.homeMatchesDescription') }}</p>
                </div>
                <div class="home-fixtures-actions">
                    <div class="home-fixtures-count">
                        <strong>{{ str_pad($homeMatches->count(), 2, '0', STR_PAD_LEFT) }}</strong>
                        <span>{{ __('frontend.homeMatchesSelected') }}</span>
                    </div>
                    <button type="button" class="home-fixtures-prev" aria-label="{{ __('frontend.heroPrevious') }}"><i class="bi bi-arrow-right"></i></button>
                    <button type="button" class="home-fixtures-next" aria-label="{{ __('frontend.heroNext') }}"><i class="bi bi-arrow-left"></i></button>
                </div>
            </header>

            <div class="home-fixtures-swiper swiper">
                <div class="swiper-wrapper">
                    @foreach($homeMatches as $homeMatch)
                    <?php
                        $homeTeam = $homeMatch->homeTeam;
                        $awayTeam = $homeMatch->awayTeam;
                        if (!$homeTeam || !$awayTeam) continue;
                        $homeName = $isEnglishMatches ? ($homeTeam->name_en ?: $homeTeam->name_ar) : ($homeTeam->name_ar ?: $homeTeam->name_en);
                        $awayName = $isEnglishMatches ? ($awayTeam->name_en ?: $awayTeam->name_ar) : ($awayTeam->name_ar ?: $awayTeam->name_en);
                        $leagueName = $isEnglishMatches
                            ? ($homeMatch->league?->name_en ?: $homeMatch->league?->name_ar)
                            : ($homeMatch->league?->name_ar ?: $homeMatch->league?->name_en);
                        $isLiveMatch = $homeMatchIsLive($homeMatch);
                        $matchState = $isLiveMatch ? 'live' : ($homeMatch->is_finished ? 'finished' : 'upcoming');
                        $homeResult = !is_null($homeMatch->ft_home_score) ? $homeMatch->ft_home_score : $homeMatch->home_score;
                        $awayResult = !is_null($homeMatch->ft_away_score) ? $homeMatch->ft_away_score : $homeMatch->away_score;
                    ?>
                    <div class="swiper-slide">
                    <article class="home-fixture-card is-{{ $matchState }}">
                        <div class="home-fixture-meta">
                            <span>{{ $leagueName }}@if($homeMatch->season?->name) <small>/ {{ $homeMatch->season->name }}</small>@endif</span>
                            <b>
                                @if($isLiveMatch)
                                    <i></i>{{ __('frontend.sliderMatchLive') }}@if($homeMatch->minute) {{ $homeMatch->minute }}′@endif
                                @elseif($homeMatch->is_finished)
                                    {{ __('frontend.sliderMatchFinished') }}
                                @else
                                    {{ __('frontend.sliderMatchUpcoming') }}
                                @endif
                            </b>
                        </div>

                        <div class="home-fixture-date">
                            <span>{{ $homeMatch->round?->name ?: __('frontend.homeMatchesMatch') }}</span>
                            <time datetime="{{ $homeMatch->starting_at?->toIso8601String() }}">
                                {{ $homeMatch->starting_at?->translatedFormat('l، d M Y') }}
                            </time>
                        </div>

                        <div class="home-fixture-teams">
                            <div class="home-fixture-team">
                                @if($homeTeam->image_path)
                                    <img src="{{ $homeTeam->image_path }}" alt="{{ $homeName }}">
                                @else
                                    <span>{{ mb_substr($homeName, 0, 1) }}</span>
                                @endif
                                <strong>{{ $homeName }}</strong>
                            </div>

                            <div class="home-fixture-result">
                                @if($homeMatch->is_finished || $isLiveMatch || !is_null($homeResult) || !is_null($awayResult))
                                    <div><b>{{ $homeResult ?? 0 }}</b><em>:</em><b>{{ $awayResult ?? 0 }}</b></div>
                                    @if(!is_null($homeMatch->pen_home) && !is_null($homeMatch->pen_away))
                                        <small>{{ __('frontend.sliderMatchPenalty') }} {{ $homeMatch->pen_home }}-{{ $homeMatch->pen_away }}</small>
                                    @endif
                                @else
                                    <time>{{ $homeMatch->starting_at?->format('H:i') }}</time>
                                    <small>{{ __('frontend.homeMatchesLocalTime') }}</small>
                                @endif
                            </div>

                            <div class="home-fixture-team">
                                @if($awayTeam->image_path)
                                    <img src="{{ $awayTeam->image_path }}" alt="{{ $awayName }}">
                                @else
                                    <span>{{ mb_substr($awayName, 0, 1) }}</span>
                                @endif
                                <strong>{{ $awayName }}</strong>
                            </div>
                        </div>

                    </article>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="home-fixtures-rail">
                <div class="home-fixtures-pagination"></div>
                <span>{{ __('frontend.homeMatchesSwipe') }}</span>
            </div>
        </div>
    </section>

    @push('after-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var slider = document.querySelector('.home-fixtures-swiper');
                if (!slider || typeof Swiper === 'undefined') return;

                var section = slider.closest('.home-fixtures');
                new Swiper(slider, {
                    speed: 700,
                    grabCursor: true,
                    watchOverflow: true,
                    slidesPerView: 1.08,
                    spaceBetween: 14,
                    navigation: {
                        nextEl: section.querySelector('.home-fixtures-next'),
                        prevEl: section.querySelector('.home-fixtures-prev')
                    },
                    pagination: {
                        el: section.querySelector('.home-fixtures-pagination'),
                        type: 'progressbar'
                    },
                    breakpoints: {
                        576: {slidesPerView: 1.4, spaceBetween: 16},
                        900: {slidesPerView: 2.1, spaceBetween: 18},
                        1300: {slidesPerView: 3, spaceBetween: 20}
                    }
                });
            });
        </script>
    @endpush
@endif
