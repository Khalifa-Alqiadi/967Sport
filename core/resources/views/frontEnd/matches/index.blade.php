@extends('frontEnd.layouts.master')

@section('bodyClass', 'sport-matches-page sport-schedule-page')

@section('content')
<div class="sm-page sm-page--schedule-v2">
    <section class="sm-hero sm-hero--schedule">
        <div class="container sm-hero__inner">
            <div class="sm-schedule-intro">
                <span class="sm-eyebrow"><i></i>{{ __('matches.matchCenter') }}</span>
                <h1>{{ __('matches.matchesSchedule') }}</h1>
                <p>{{ __('matches.scheduleIntro') }}</p>
                <div class="sm-schedule-snapshot">
                    <span><b>{{ $fixturesCount }}</b>{{ __('matches.match') }}</span>
                    <span><b>{{ $fixturesByLeague->count() }}</b>{{ __('matches.competition') }}</span>
                </div>
            </div>
            <div class="sm-date-ticket">
                <span>{{ $selectedDate->translatedFormat('l') }}</span>
                <strong>{{ $selectedDate->format('d') }}</strong>
                <small>{{ $selectedDate->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </section>

    <section class="sm-section sm-section--schedule">
        <div class="container">
            <nav class="sm-day-tabs" aria-label="{{ __('matches.chooseDate') }}">
                <a class="{{ $day === 'yesterday' ? 'is-active' : '' }}" href="{{ route('sport.matches', ['day' => 'yesterday']) }}"><span>{{ __('matches.yesterday') }}</span><b>{{ now()->subDay()->translatedFormat('d M') }}</b></a>
                <a class="{{ $day === 'today' ? 'is-active' : '' }}" href="{{ route('sport.matches', ['day' => 'today']) }}"><span>{{ __('matches.today') }}</span><b>{{ now()->translatedFormat('d M') }}</b></a>
                <a class="{{ $day === 'tomorrow' ? 'is-active' : '' }}" href="{{ route('sport.matches', ['day' => 'tomorrow']) }}"><span>{{ __('matches.tomorrow') }}</span><b>{{ now()->addDay()->translatedFormat('d M') }}</b></a>
                <button type="button" class="{{ $day === 'date' ? 'is-active' : '' }}" data-date-toggle><span>{{ __('matches.byDate') }}</span><b><i class="bi bi-calendar3"></i></b></button>
            </nav>
            <form class="sm-date-filter {{ $day === 'date' ? 'is-open' : '' }}" action="{{ route('sport.matches') }}" method="GET" data-date-form>
                <input type="hidden" name="day" value="date">
                <label for="match-date">{{ __('matches.selectDate') }}</label>
                <input id="match-date" type="date" name="date" value="{{ $selectedDate->toDateString() }}" required>
                <button type="submit">{{ __('matches.showMatches') }} <i class="bi bi-arrow-up-left"></i></button>
            </form>

            <div class="sm-results-heading">
                <div><span>{{ $selectedDate->translatedFormat('l، d F') }}</span><h2>{{ __('matches.dayMatches') }}</h2></div>
                <strong>{{ $fixturesCount }} <small>{{ __('matches.match') }}</small></strong>
            </div>

            @forelse($fixturesByLeague as $leagueFixtures)
                @php
                    $groupLeague = $leagueFixtures->first()?->league;
                    $groupLeagueName = app()->getLocale() === 'en' ? ($groupLeague?->name_en ?: $groupLeague?->name_ar) : ($groupLeague?->name_ar ?: $groupLeague?->name_en);
                @endphp
                <section class="sm-league-group">
                    <header>
                        <span class="sm-mini-logo">@if($groupLeague?->image_path)<img src="{{ $groupLeague->image_path }}" alt="">@else<i class="bi bi-trophy"></i>@endif</span>
                        <div><small>{{ __('matches.competition') }}</small><h3>{{ $groupLeagueName ?: __('matches.unknownCompetition') }}</h3></div>
                        @if($groupLeague)<a href="{{ route('sport.league', $groupLeague->id) }}">{{ __('matches.competitionPage') }} <i class="bi bi-arrow-up-left"></i></a>@endif
                    </header>
                    <div class="sm-fixtures-grid">
                        @foreach($leagueFixtures as $fixture)
                            @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture])
                        @endforeach
                    </div>
                </section>
            @empty
                @include('frontEnd.matches.partials.empty')
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('after-styles')
<link href="{{ URL::asset('assets/frontend/css/967sport-matches.css') }}?v={{ Helper::system_version() }}&ui=2" rel="stylesheet">
@endpush

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-date-toggle]');
    const form = document.querySelector('[data-date-form]');
    if (toggle && form) toggle.addEventListener('click', function () { form.classList.toggle('is-open'); });
});
</script>
@endpush
