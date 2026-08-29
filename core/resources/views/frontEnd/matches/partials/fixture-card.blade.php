@php
    $isEnglish = app()->getLocale() === 'en';
    $homeName = $isEnglish ? ($fixture->homeTeam?->name_en ?: $fixture->homeTeam?->name_ar) : ($fixture->homeTeam?->name_ar ?: $fixture->homeTeam?->name_en);
    $awayName = $isEnglish ? ($fixture->awayTeam?->name_en ?: $fixture->awayTeam?->name_ar) : ($fixture->awayTeam?->name_ar ?: $fixture->awayTeam?->name_en);
    $leagueName = $isEnglish ? ($fixture->league?->name_en ?: $fixture->league?->name_ar) : ($fixture->league?->name_ar ?: $fixture->league?->name_en);
    $isLive = !$fixture->is_finished && $fixture->starting_at?->isPast() && in_array(strtoupper((string) $fixture->state_code), ['LIVE', 'INPLAY', 'HT', 'ET', 'BREAK'], true);
    $hasStarted = $fixture->is_finished || $isLive || $fixture->starting_at?->isPast();
    $homeScore = $fixture->home_score ?? $fixture->ft_home_score;
    $awayScore = $fixture->away_score ?? $fixture->ft_away_score;
@endphp
<a class="sm-fixture-card {{ $isLive ? 'is-live' : ($fixture->is_finished ? 'is-finished' : 'is-upcoming') }}" href="{{ route('sport.match', $fixture->id) }}">
    <span class="sm-fixture-card__accent" aria-hidden="true"></span>
    <div class="sm-fixture-card__meta">
        <span>{{ $leagueName ?: __('matches.competition') }}</span>
        <span>{{ $fixture->round?->name ?: __('matches.match') }}</span>
    </div>
    <div class="sm-fixture-card__contest">
        <div class="sm-fixture-team">
            <span class="sm-team-mark">
                @if($fixture->homeTeam?->image_path)
                    <img src="{{ $fixture->homeTeam->image_path }}" alt="{{ $homeName }}" loading="lazy">
                @else
                    <b>{{ mb_substr($homeName ?: __('matches.unknown'), 0, 1) }}</b>
                @endif
            </span>
            <strong>{{ $homeName ?: __('matches.unknown') }}</strong>
        </div>
        <div class="sm-fixture-score">
            @if($hasStarted && $homeScore !== null && $awayScore !== null)
                <strong dir="ltr">{{ $homeScore }} <i>:</i> {{ $awayScore }}</strong>
            @else
                <strong dir="ltr">{{ optional($fixture->starting_at)->format('H:i') ?: '--:--' }}</strong>
            @endif
            <span class="{{ $isLive ? 'is-live' : '' }}">
                @if($isLive)
                    {{ __('matches.live') }} {{ $fixture->minute ? $fixture->minute."'" : '' }}
                @elseif($fixture->is_finished)
                    {{ __('matches.finished') }}
                @else
                    {{ __('matches.yemenTime') }}
                @endif
            </span>
        </div>
        <div class="sm-fixture-team">
            <span class="sm-team-mark">
                @if($fixture->awayTeam?->image_path)
                    <img src="{{ $fixture->awayTeam->image_path }}" alt="{{ $awayName }}" loading="lazy">
                @else
                    <b>{{ mb_substr($awayName ?: __('matches.unknown'), 0, 1) }}</b>
                @endif
            </span>
            <strong>{{ $awayName ?: __('matches.unknown') }}</strong>
        </div>
    </div>
    <div class="sm-fixture-card__foot">
        <span>{{ optional($fixture->starting_at)->translatedFormat('l، d F Y') }}</span>
        <span>{{ __('matches.matchDetails') }} <i class="bi bi-arrow-up-left"></i></span>
    </div>
</a>
