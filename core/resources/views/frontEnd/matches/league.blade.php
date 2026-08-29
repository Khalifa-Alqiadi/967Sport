@extends('frontEnd.layouts.master')

@section('bodyClass', 'sport-matches-page sport-league-page')

@php
    $isEnglish = app()->getLocale() === 'en';
    $leagueName = $isEnglish ? ($league->name_en ?: $league->name_ar) : ($league->name_ar ?: $league->name_en);
    $tabs = ['overview', 'matches', 'news', 'statistics', 'standings'];
@endphp

@section('content')
<div class="sm-page sm-page--league-v2">
    <section class="sm-league-hero">
        <span class="sm-league-hero__watermark" aria-hidden="true">967</span>
        <div class="container">
            <div class="sm-league-hero__crumbs">
                <a href="{{ route('sport.competitions') }}">{{ __('matches.competitions') }}</a><i class="bi bi-chevron-left"></i><span>{{ $leagueName }}</span>
            </div>
            <div class="sm-league-hero__main">
                <div class="sm-league-badge">@if($league->image_path)<img src="{{ $league->image_path }}" alt="{{ $leagueName }}">@else<i class="bi bi-trophy"></i>@endif</div>
                <div class="sm-league-title">
                    <span class="sm-eyebrow"><i></i>{{ __('matches.officialCompetitionPage') }}</span>
                    <h1>{{ $leagueName }}</h1>
                    <p>{{ $season?->name ?: __('matches.currentSeason') }}</p>
                    <div class="sm-league-title__facts">
                        <span><b>{{ $statistics['teams'] }}</b>{{ __('matches.team') }}</span>
                        <span><b>{{ $statistics['matches'] }}</b>{{ __('matches.match') }}</span>
                        <span><b>{{ $statistics['goals'] }}</b>{{ __('matches.goals') }}</span>
                    </div>
                </div>
                @if($seasons->isNotEmpty())
                    <form class="sm-season-switcher" action="{{ route('sport.league', $league->id) }}" method="GET">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <label for="league-season">{{ __('matches.season') }}</label>
                        <select id="league-season" name="season" onchange="this.form.submit()">
                            @foreach($seasons as $seasonOption)<option value="{{ $seasonOption->id }}" {{ $season?->id === $seasonOption->id ? 'selected' : '' }}>{{ $seasonOption->name }}</option>@endforeach
                        </select>
                    </form>
                @endif
            </div>
            <nav class="sm-league-tabs" aria-label="{{ __('matches.leagueSections') }}">
                @foreach($tabs as $tabName)
                    <a class="{{ $tab === $tabName ? 'is-active' : '' }}" href="{{ route('sport.league', ['league' => $league->id, 'tab' => $tabName, 'season' => $season?->id]) }}"><i class="bi {{ ['overview' => 'bi-grid', 'matches' => 'bi-calendar2-week', 'news' => 'bi-newspaper', 'statistics' => 'bi-bar-chart', 'standings' => 'bi-list-ol'][$tabName] }}"></i>{{ __('matches.tabs.'.$tabName) }}</a>
                @endforeach
            </nav>
        </div>
    </section>

    <section class="sm-section sm-league-content">
        <div class="container">
            @if($tab === 'overview')
                <div class="sm-overview-heading">
                    <div>
                        <span class="sm-eyebrow"><i></i>{{ __('matches.officialCompetitionPage') }}</span>
                        <h2>{{ __('matches.seasonNumbers') }}</h2>
                    </div>
                    <span class="sm-overview-season"><i class="bi bi-trophy"></i>{{ $season?->name ?: __('matches.currentSeason') }}</span>
                </div>
                <div class="sm-stat-strip">
                    <article data-stat="01"><i class="bi bi-calendar2-week"></i><span>{{ __('matches.matchesPlayed') }}</span><strong>{{ $statistics['matches'] }}</strong><small>{{ __('matches.registeredMatch') }}</small></article>
                    <article data-stat="02"><i class="bi bi-check2-circle"></i><span>{{ __('matches.finishedMatches') }}</span><strong>{{ $statistics['finished'] }}</strong><small>{{ __('matches.finalWhistle') }}</small></article>
                    <article data-stat="03"><i class="bi bi-bullseye"></i><span>{{ __('matches.goals') }}</span><strong>{{ $statistics['goals'] }}</strong><small>{{ __('matches.scoredGoal') }}</small></article>
                    <article data-stat="04"><i class="bi bi-shield-check"></i><span>{{ __('matches.participatingTeams') }}</span><strong>{{ $statistics['teams'] }}</strong><small>{{ __('matches.team') }}</small></article>
                </div>
                <div class="sm-overview-grid">
                    <div class="sm-overview-panel sm-overview-panel--fixtures">
                        <div class="sm-section-heading sm-section-heading--compact"><div><span>{{ __('matches.nextRound') }}</span><h2>{{ __('matches.upcomingMatches') }}</h2></div><a href="{{ route('sport.league', ['league' => $league->id, 'tab' => 'matches', 'season' => $season?->id]) }}">{{ __('matches.viewAll') }}</a></div>
                        <div class="sm-fixtures-stack">
                            @forelse($upcomingMatches as $fixture) @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture]) @empty @include('frontEnd.matches.partials.empty') @endforelse
                        </div>
                    </div>
                    <aside class="sm-overview-panel sm-overview-panel--table">
                        <div class="sm-section-heading sm-section-heading--compact"><div><span>{{ __('matches.table') }}</span><h2>{{ __('matches.latestStanding') }}</h2></div><a href="{{ route('sport.league', ['league' => $league->id, 'tab' => 'standings', 'season' => $season?->id]) }}">{{ __('matches.fullTable') }}</a></div>
                        @include('frontEnd.matches.partials.standings-table', ['standingRows' => $standings->take(6), 'compact' => true])
                    </aside>
                </div>
                @if($news->isNotEmpty())
                    <div class="sm-news-section">
                        <div class="sm-section-heading"><div><span>{{ __('matches.coverage') }}</span><h2>{{ __('matches.latestCompetitionNews') }}</h2></div><a href="{{ route('sport.league', ['league' => $league->id, 'tab' => 'news', 'season' => $season?->id]) }}">{{ __('matches.allNews') }}</a></div>
                        @include('frontEnd.matches.partials.news-grid', ['newsItems' => $news])
                    </div>
                @endif
            @elseif($tab === 'matches')
                <div class="sm-section-heading"><div><span>{{ $season?->name }}</span><h2>{{ __('matches.competitionMatches') }}</h2></div></div>
                <div class="sm-match-columns">
                    <div><h3><i class="bi bi-clock"></i>{{ __('matches.upcomingMatches') }}</h3><div class="sm-fixtures-stack">@forelse($upcomingMatches as $fixture) @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture]) @empty @include('frontEnd.matches.partials.empty') @endforelse</div></div>
                    <div><h3><i class="bi bi-check2-circle"></i>{{ __('matches.results') }}</h3><div class="sm-fixtures-stack">@forelse($completedMatches as $fixture) @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture]) @empty @include('frontEnd.matches.partials.empty') @endforelse</div></div>
                </div>
            @elseif($tab === 'news')
                <div class="sm-section-heading"><div><span>{{ __('matches.mediaCenter') }}</span><h2>{{ __('matches.competitionNews') }}</h2></div></div>
                @if($news->isNotEmpty()) @include('frontEnd.matches.partials.news-grid', ['newsItems' => $news]) @else @include('frontEnd.matches.partials.empty', ['title' => __('matches.noNews'), 'description' => __('matches.noNewsDescription')]) @endif
            @elseif($tab === 'statistics')
                <div class="sm-section-heading"><div><span>{{ $season?->name }}</span><h2>{{ __('matches.seasonNumbers') }}</h2></div></div>
                <div class="sm-statistics-board">
                    <article><i class="bi bi-calendar-check"></i><span>{{ __('matches.totalMatches') }}</span><strong>{{ $statistics['matches'] }}</strong><small>{{ __('matches.registeredMatch') }}</small></article>
                    <article><i class="bi bi-check2-circle"></i><span>{{ __('matches.finishedMatches') }}</span><strong>{{ $statistics['finished'] }}</strong><small>{{ __('matches.finalWhistle') }}</small></article>
                    <article><i class="bi bi-bullseye"></i><span>{{ __('matches.totalGoals') }}</span><strong>{{ $statistics['goals'] }}</strong><small>{{ __('matches.scoredGoal') }}</small></article>
                    <article><i class="bi bi-shield"></i><span>{{ __('matches.participatingTeams') }}</span><strong>{{ $statistics['teams'] }}</strong><small>{{ __('matches.team') }}</small></article>
                </div>
            @else
                <div class="sm-section-heading"><div><span>{{ $season?->name }}</span><h2>{{ __('matches.standings') }}</h2></div></div>
                @include('frontEnd.matches.partials.standings-table', ['standingRows' => $standings, 'compact' => false])
            @endif
        </div>
    </section>
</div>
@endsection

@push('after-styles')
<link href="{{ URL::asset('assets/frontend/css/967sport-matches.css') }}?v={{ Helper::system_version() }}&ui=4" rel="stylesheet">
@endpush
