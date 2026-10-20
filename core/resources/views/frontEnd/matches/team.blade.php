@extends('frontEnd.layouts.master')

@section('bodyClass', 'sport-matches-page sport-profile-page')

@php
    $isEnglish = app()->getLocale() === 'en';
    $teamName = $isEnglish ? ($team->name_en ?: $team->name_ar) : ($team->name_ar ?: $team->name_en);
    $countryName = $isEnglish ? ($team->country?->title_en ?: $team->country?->title_ar) : ($team->country?->title_ar ?: $team->country?->title_en);
    $venueName = $isEnglish ? ($team->venue?->name_en ?: $team->venue?->name_ar) : ($team->venue?->name_ar ?: $team->venue?->name_en);
    $tabs = ['overview', 'matches', 'squad', 'news'];
    $tabUrl = fn ($name) => $isEnglish
        ? route('sport.localized.team', ['lang' => 'en', 'team' => $team->id, 'tab' => $name])
        : route('sport.team', ['team' => $team->id, 'tab' => $name]);
@endphp

@section('content')
<div class="sm-page fp-page">
    <header class="fp-hero">
        <div class="container">
            <div class="fp-breadcrumb"><a href="{{ $isEnglish ? route('sport.localized.competitions', ['lang' => 'en']) : route('sport.competitions') }}">{{ __('matches.competitions') }}</a><i class="bi bi-chevron-left" aria-hidden="true"></i><span>{{ $teamName }}</span></div>
            <div class="fp-hero__main">
                <div class="fp-identity-mark">
                    @if($team->image_path)<img src="{{ $team->image_path }}" alt="{{ $teamName }}">
                    @else<i class="bi bi-shield-fill" aria-hidden="true"></i>@endif
                </div>
                <div class="fp-hero__copy">
                    <span class="fp-kicker">{{ __('footballProfiles.teamProfile') }}</span>
                    <h1>{{ $teamName }}</h1>
                    <div class="fp-hero__meta">
                        @if($countryName)<span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $countryName }}</span>@endif
                        @if($team->founded)<span><i class="bi bi-calendar3" aria-hidden="true"></i>{{ __('footballProfiles.founded') }} {{ $team->founded }}</span>@endif
                        @if($venueName)<span><i class="bi bi-building" aria-hidden="true"></i>{{ $venueName }}</span>@endif
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="fp-nav-wrap"><div class="container"><nav class="fp-nav" aria-label="{{ __('footballProfiles.teamSections') }}">
        @foreach($tabs as $name)
            <a href="{{ $tabUrl($name) }}" class="{{ $tab === $name ? 'is-active' : '' }}" @if($tab === $name) aria-current="page" @endif>{{ __('footballProfiles.'.$name) }}</a>
        @endforeach
    </nav></div></div>

    <section class="fp-content"><div class="container">
        @if($tab === 'overview')
            <div class="fp-metrics">
                <div><strong>{{ $stats['played'] }}</strong><span>{{ __('footballProfiles.played') }}</span></div>
                <div><strong>{{ $stats['wins'] }}</strong><span>{{ __('footballProfiles.wins') }}</span></div>
                <div><strong>{{ $stats['draws'] }}</strong><span>{{ __('footballProfiles.draws') }}</span></div>
                <div><strong>{{ $stats['goals'] }}</strong><span>{{ __('footballProfiles.goals') }}</span></div>
            </div>
            <div class="fp-columns">
                <section class="fp-panel">
                    <div class="fp-heading"><h2>{{ __('footballProfiles.nextMatches') }}</h2><a href="{{ $tabUrl('matches') }}">{{ __('footballProfiles.allMatches') }} <i class="bi bi-arrow-up-left" aria-hidden="true"></i></a></div>
                    <div class="fp-fixtures">@forelse($upcomingMatches as $fixture) @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture]) @empty <p class="fp-empty">{{ __('footballProfiles.noMatches') }}</p> @endforelse</div>
                </section>
                <div class="fp-column-stack">
                    @if($standings->isNotEmpty())
                        <section class="fp-panel fp-standing"><div class="fp-heading"><h2>{{ __('footballProfiles.seasonStanding') }}</h2></div>
                            @foreach($standings->take(2) as $row)
                                <div class="fp-standing__row"><span>{{ $isEnglish ? ($row->league?->name_en ?: $row->league?->name_ar) : ($row->league?->name_ar ?: $row->league?->name_en) }}<small>{{ $row->season?->name }}</small></span><strong>{{ $row->position ?: '–' }}<small>{{ __('footballProfiles.position') }}</small></strong><strong>{{ $row->points }}<small>{{ __('footballProfiles.points') }}</small></strong></div>
                            @endforeach
                        </section>
                    @endif
                    <section class="fp-panel fp-facts"><div class="fp-heading"><h2>{{ __('footballProfiles.teamFacts') }}</h2></div><dl>
                        @if($countryName)<div><dt>{{ __('footballProfiles.country') }}</dt><dd>{{ $countryName }}</dd></div>@endif
                        @if($team->founded)<div><dt>{{ __('footballProfiles.founded') }}</dt><dd>{{ $team->founded }}</dd></div>@endif
                        @if($venueName)<div><dt>{{ __('footballProfiles.venue') }}</dt><dd>{{ $venueName }}</dd></div>@endif
                        <div><dt>{{ __('footballProfiles.squad') }}</dt><dd>{{ $squad->count() }}</dd></div>
                    </dl></section>
                </div>
            </div>
            @if($squad->isNotEmpty())<section class="fp-section"><div class="fp-heading"><h2>{{ __('footballProfiles.squad') }}</h2><a href="{{ $tabUrl('squad') }}">{{ __('footballProfiles.allPlayers') }} <i class="bi bi-arrow-up-left" aria-hidden="true"></i></a></div><div class="fp-player-grid">@foreach($squad->take(6) as $squadPlayer) @include('frontEnd.matches.partials.player-card') @endforeach</div></section>@endif
            @if($news->isNotEmpty())<section class="fp-section"><div class="fp-heading"><h2>{{ __('footballProfiles.latestNews') }}</h2><a href="{{ $tabUrl('news') }}">{{ __('footballProfiles.allNews') }} <i class="bi bi-arrow-up-left" aria-hidden="true"></i></a></div>@include('frontEnd.matches.partials.news-grid', ['newsItems' => $news])</section>@endif
        @elseif($tab === 'matches')
            <div class="fp-match-columns"><section><div class="fp-heading"><h2>{{ __('footballProfiles.nextMatches') }}</h2></div><div class="fp-fixtures">@forelse($upcomingMatches as $fixture) @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture]) @empty <p class="fp-empty">{{ __('footballProfiles.noMatches') }}</p> @endforelse</div></section><section><div class="fp-heading"><h2>{{ __('footballProfiles.recentResults') }}</h2></div><div class="fp-fixtures">@forelse($completedMatches as $fixture) @include('frontEnd.matches.partials.fixture-card', ['fixture' => $fixture]) @empty <p class="fp-empty">{{ __('footballProfiles.noMatches') }}</p> @endforelse</div></section></div>
        @elseif($tab === 'squad')
            <div class="fp-heading"><h2>{{ __('footballProfiles.squad') }}</h2></div>
            @if($squad->isNotEmpty())<div class="fp-player-grid">@foreach($squad as $squadPlayer) @include('frontEnd.matches.partials.player-card') @endforeach</div>@else<p class="fp-empty">{{ __('footballProfiles.noSquad') }}</p>@endif
        @else
            <div class="fp-heading"><h2>{{ __('footballProfiles.latestNews') }}</h2></div>
            @if($news->isNotEmpty()) @include('frontEnd.matches.partials.news-grid', ['newsItems' => $news]) @else <p class="fp-empty">{{ __('footballProfiles.noNews') }}</p> @endif
        @endif
    </div></section>
</div>
@endsection

@push('after-styles')
<link href="{{ URL::asset('assets/frontend/css/967sport-matches.css') }}?v={{ Helper::system_version() }}" rel="stylesheet">
<link href="{{ URL::asset('assets/frontend/css/967sport-profiles.css') }}?v={{ Helper::system_version() }}" rel="stylesheet">
@endpush
