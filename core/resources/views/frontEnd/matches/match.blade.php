@extends('frontEnd.layouts.master')

@section('bodyClass', 'sport-matches-page sport-match-page')

@php
    $isEnglish = app()->getLocale() === 'en';
    $homeName = $isEnglish ? ($fixture->homeTeam?->name_en ?: $fixture->homeTeam?->name_ar) : ($fixture->homeTeam?->name_ar ?: $fixture->homeTeam?->name_en);
    $awayName = $isEnglish ? ($fixture->awayTeam?->name_en ?: $fixture->awayTeam?->name_ar) : ($fixture->awayTeam?->name_ar ?: $fixture->awayTeam?->name_en);
    $leagueName = $isEnglish ? ($fixture->league?->name_en ?: $fixture->league?->name_ar) : ($fixture->league?->name_ar ?: $fixture->league?->name_en);
    $isLive = !$fixture->is_finished && $fixture->starting_at?->isPast() && in_array(strtoupper((string) $fixture->state_code), ['LIVE', 'INPLAY', 'HT', 'ET', 'BREAK'], true);
    $hasStarted = $fixture->is_finished || $isLive || $fixture->starting_at?->isPast();
    $homeScore = $fixture->home_score ?? $fixture->ft_home_score;
    $awayScore = $fixture->away_score ?? $fixture->ft_away_score;
    $venue = data_get($fixture->venue_json, 'name') ?: data_get($fixture->venue_json, 'name_ar');
@endphp

@section('content')
<div class="sm-page">
    <section class="sm-match-hero">
        <div class="container">
            <div class="sm-match-crumbs">
                <a href="{{ route('sport.matches') }}">{{ __('matches.matches') }}</a><i class="bi bi-chevron-left"></i>
                @if($fixture->league)<a href="{{ route('sport.league', $fixture->league_id) }}">{{ $leagueName }}</a>@endif
            </div>
            <div class="sm-match-status {{ $isLive ? 'is-live' : '' }}">
                <i></i>
                @if($isLive){{ __('matches.liveNow') }} {{ $fixture->minute ? $fixture->minute."'" : '' }}@elseif($fixture->is_finished){{ __('matches.finished') }}@else{{ __('matches.upcomingMatch') }}@endif
            </div>
            <div class="sm-scoreboard">
                <div class="sm-scoreboard__team">
                    <span>@if($fixture->homeTeam?->image_path)<img src="{{ $fixture->homeTeam->image_path }}" alt="{{ $homeName }}">@else<b>{{ mb_substr($homeName ?: __('matches.unknown'), 0, 1) }}</b>@endif</span>
                    <h1>{{ $homeName ?: __('matches.unknown') }}</h1><small>{{ __('matches.homeTeam') }}</small>
                </div>
                <div class="sm-scoreboard__center">
                    <span>{{ optional($fixture->starting_at)->translatedFormat('l، d F Y') }}</span>
                    @if($hasStarted && $homeScore !== null && $awayScore !== null)
                        <strong dir="ltr">{{ $homeScore }} <i>:</i> {{ $awayScore }}</strong>
                    @else
                        <strong class="is-time" dir="ltr">{{ optional($fixture->starting_at)->format('H:i') ?: '--:--' }}</strong>
                    @endif
                    <small>{{ $fixture->round?->name ?: ($fixture->season?->name ?: __('matches.match')) }}</small>
                    @if($fixture->pen_home !== null && $fixture->pen_away !== null)<em>{{ __('matches.penalties') }}: <b dir="ltr">{{ $fixture->pen_home }} - {{ $fixture->pen_away }}</b></em>@endif
                </div>
                <div class="sm-scoreboard__team">
                    <span>@if($fixture->awayTeam?->image_path)<img src="{{ $fixture->awayTeam->image_path }}" alt="{{ $awayName }}">@else<b>{{ mb_substr($awayName ?: __('matches.unknown'), 0, 1) }}</b>@endif</span>
                    <h1>{{ $awayName ?: __('matches.unknown') }}</h1><small>{{ __('matches.awayTeam') }}</small>
                </div>
            </div>
            <div class="sm-match-facts">
                <span><i class="bi bi-trophy"></i><small>{{ __('matches.competition') }}</small><strong>{{ $leagueName ?: __('matches.unknown') }}</strong></span>
                <span><i class="bi bi-clock"></i><small>{{ __('matches.kickoff') }}</small><strong dir="ltr">{{ optional($fixture->starting_at)->format('H:i') ?: '--:--' }}</strong></span>
                <span><i class="bi bi-geo-alt"></i><small>{{ __('matches.stadium') }}</small><strong>{{ $venue ?: __('matches.notSpecified') }}</strong></span>
            </div>
        </div>
    </section>

    <section class="sm-section sm-match-content">
        <div class="container sm-match-layout">
            <div class="sm-match-main">
                <div class="sm-section-heading"><div><span>{{ __('matches.matchStory') }}</span><h2>{{ __('matches.matchEvents') }}</h2></div></div>
                @if($fixture->events->isNotEmpty())
                    <div class="sm-timeline">
                        @foreach($fixture->events as $event)
                            @php
                                $eventTeamName = $isEnglish ? ($event->team?->name_en ?: $event->team?->name_ar) : ($event->team?->name_ar ?: $event->team?->name_en);
                                $playerName = $isEnglish ? ($event->player?->name_en ?: $event->player?->name_ar) : ($event->player?->name_ar ?: $event->player?->name_en);
                                $playerOutName = $isEnglish ? ($event->playerOut?->name_en ?: $event->playerOut?->name_ar) : ($event->playerOut?->name_ar ?: $event->playerOut?->name_en);
                                $playerInName = $isEnglish ? ($event->playerIn?->name_en ?: $event->playerIn?->name_ar) : ($event->playerIn?->name_ar ?: $event->playerIn?->name_en);
                                $eventPlayerName = $event->type === 'substitution'
                                    ? trim(($playerOutName ?: __('matches.unknownPlayer')).' ← '.($playerInName ?: __('matches.unknownPlayer')))
                                    : ($playerName ?: $event->info ?: __('matches.unknownPlayer'));
                                $eventIcon = match ($event->type) {
                                    'goal', 'own_goal', 'penalty' => 'bi-bullseye',
                                    'yellow_card', 'red_card' => 'bi-square-fill',
                                    'substitution' => 'bi-arrow-left-right',
                                    default => 'bi-circle-fill',
                                };
                            @endphp
                            <article class="{{ $event->team_id === $fixture->away_team_id ? 'is-away' : 'is-home' }}">
                                <time>{{ $event->minute }}{{ $event->extra_minute ? '+'.$event->extra_minute : '' }}'</time>
                                <span class="sm-event-icon"><i class="bi {{ $eventIcon }}"></i></span>
                                <div><small>{{ __('matches.events.'.$event->type) }}</small><strong>{{ $eventPlayerName }}</strong><em>{{ $eventTeamName }}</em></div>
                            </article>
                        @endforeach
                    </div>
                @else
                    @include('frontEnd.matches.partials.empty', ['title' => __('matches.noEvents'), 'description' => __('matches.noEventsDescription')])
                @endif

                @if($news->isNotEmpty())
                    <div class="sm-news-section"><div class="sm-section-heading"><div><span>{{ __('matches.coverage') }}</span><h2>{{ __('matches.matchNews') }}</h2></div></div>@include('frontEnd.matches.partials.news-grid', ['newsItems' => $news])</div>
                @endif
            </div>
            <aside class="sm-match-side">
                <div class="sm-side-panel">
                    <h2>{{ __('matches.matchSummary') }}</h2>
                    <dl>
                        <div><dt>{{ __('matches.halfTime') }}</dt><dd dir="ltr">{{ $fixture->ht_home_score ?? '-' }} - {{ $fixture->ht_away_score ?? '-' }}</dd></div>
                        <div><dt>{{ __('matches.fullTime') }}</dt><dd dir="ltr">{{ $fixture->ft_home_score ?? $homeScore ?? '-' }} - {{ $fixture->ft_away_score ?? $awayScore ?? '-' }}</dd></div>
                        @if($fixture->et_home_score !== null || $fixture->et_away_score !== null)<div><dt>{{ __('matches.extraTime') }}</dt><dd dir="ltr">{{ $fixture->et_home_score ?? '-' }} - {{ $fixture->et_away_score ?? '-' }}</dd></div>@endif
                    </dl>
                </div>
                @if($relatedMatches->isNotEmpty())
                    <div class="sm-related"><h2>{{ __('matches.otherMatches') }}</h2>@foreach($relatedMatches as $related)<div>@include('frontEnd.matches.partials.fixture-card', ['fixture' => $related])</div>@endforeach</div>
                @endif
            </aside>
        </div>
    </section>
</div>
@endsection

@push('after-styles')
<link href="{{ URL::asset('assets/frontend/css/967sport-matches.css') }}?v={{ Helper::system_version() }}" rel="stylesheet">
@endpush
