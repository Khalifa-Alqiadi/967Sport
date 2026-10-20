@if($eventItems->isNotEmpty())
    <div class="fp-event-list">
        @foreach($eventItems as $playerEvent)
            @php
                $eventFixture = $playerEvent->fixture;
                $eventLeagueName = app()->getLocale() === 'en'
                    ? ($eventFixture?->league?->name_en ?: $eventFixture?->league?->name_ar)
                    : ($eventFixture?->league?->name_ar ?: $eventFixture?->league?->name_en);
                $homeName = app()->getLocale() === 'en'
                    ? ($eventFixture?->homeTeam?->name_en ?: $eventFixture?->homeTeam?->name_ar)
                    : ($eventFixture?->homeTeam?->name_ar ?: $eventFixture?->homeTeam?->name_en);
                $awayName = app()->getLocale() === 'en'
                    ? ($eventFixture?->awayTeam?->name_en ?: $eventFixture?->awayTeam?->name_ar)
                    : ($eventFixture?->awayTeam?->name_ar ?: $eventFixture?->awayTeam?->name_en);
                $eventLabel = __('matches.events.'.$playerEvent->type);
            @endphp
            @if($eventFixture)
                <a class="fp-event" href="{{ route('sport.match', $eventFixture->id) }}">
                    <time>{{ $playerEvent->minute }}{{ $playerEvent->extra_minute ? '+'.$playerEvent->extra_minute : '' }}'</time>
                    <span><strong>{{ $eventLabel }}</strong><small>{{ $homeName ?: __('footballProfiles.unknown') }} – {{ $awayName ?: __('footballProfiles.unknown') }}</small></span>
                    <span class="fp-event__competition">{{ $eventLeagueName }}</span>
                    <i class="bi bi-arrow-up-left" aria-hidden="true"></i>
                </a>
            @endif
        @endforeach
    </div>
@else
    <p class="fp-empty">{{ __('footballProfiles.noEvents') }}</p>
@endif
