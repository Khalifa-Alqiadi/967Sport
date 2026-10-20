@extends('frontEnd.layouts.master')

@section('bodyClass', 'sport-matches-page sport-profile-page')

@php
    $isEnglish = app()->getLocale() === 'en';
    $playerName = $isEnglish ? ($player->name_en ?: $player->name_ar) : ($player->name_ar ?: $player->name_en);
    $countryName = $isEnglish ? ($player->country?->title_en ?: $player->country?->title_ar) : ($player->country?->title_ar ?: $player->country?->title_en);
    $tabs = ['overview', 'matches', 'career', 'news'];
    $tabUrl = fn ($name) => $isEnglish
        ? route('sport.localized.player', ['lang' => 'en', 'player' => $player->id, 'tab' => $name])
        : route('sport.player', ['player' => $player->id, 'tab' => $name]);
    $teamUrl = fn ($teamId) => $isEnglish
        ? route('sport.localized.team', ['lang' => 'en', 'team' => $teamId])
        : route('sport.team', $teamId);
    $birthDate = $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth) : null;
@endphp

@section('content')
<div class="sm-page fp-page fp-page--player">
    <header class="fp-hero">
        <div class="container">
            <div class="fp-breadcrumb"><a href="{{ $isEnglish ? route('sport.localized.competitions', ['lang' => 'en']) : route('sport.competitions') }}">{{ __('matches.competitions') }}</a><i class="bi bi-chevron-left" aria-hidden="true"></i><span>{{ $playerName }}</span></div>
            <div class="fp-hero__main">
                <div class="fp-identity-mark fp-identity-mark--person">
                    @if($player->image_path)<img src="{{ $player->image_path }}" alt="{{ $playerName }}">
                    @else<i class="bi bi-person-fill" aria-hidden="true"></i>@endif
                </div>
                <div class="fp-hero__copy">
                    <span class="fp-kicker">{{ __('footballProfiles.playerProfile') }}</span>
                    <h1>{{ $playerName ?: __('footballProfiles.unknown') }}</h1>
                    <div class="fp-hero__meta">
                        @if($countryName)<span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $countryName }}</span>@endif
                        @if($birthDate)<span><i class="bi bi-calendar3" aria-hidden="true"></i>{{ $birthDate->age }} {{ __('footballProfiles.years') }}</span>@endif
                        @foreach($teams->take(2) as $currentTeam)
                            <a href="{{ $teamUrl($currentTeam->id) }}"><i class="bi bi-shield" aria-hidden="true"></i>{{ $isEnglish ? ($currentTeam->name_en ?: $currentTeam->name_ar) : ($currentTeam->name_ar ?: $currentTeam->name_en) }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="fp-nav-wrap"><div class="container"><nav class="fp-nav" aria-label="{{ __('footballProfiles.playerSections') }}">
        @foreach($tabs as $name)
            <a href="{{ $tabUrl($name) }}" class="{{ $tab === $name ? 'is-active' : '' }}" @if($tab === $name) aria-current="page" @endif>{{ __('footballProfiles.'.$name) }}</a>
        @endforeach
    </nav></div></div>

    <section class="fp-content"><div class="container">
        @if($tab === 'overview')
            <div class="fp-metrics fp-metrics--three">
                <div><strong>{{ $stats['goals'] }}</strong><span>{{ __('footballProfiles.goals') }}</span></div>
                <div><strong>{{ $stats['assists'] }}</strong><span>{{ __('footballProfiles.assists') }}</span></div>
                <div><strong>{{ $stats['appearances'] }}</strong><span>{{ __('footballProfiles.documentedMatches') }}</span></div>
            </div>
            <div class="fp-columns">
                <section class="fp-panel">
                    <div class="fp-heading"><h2>{{ __('footballProfiles.playerEvents') }}</h2><a href="{{ $tabUrl('matches') }}">{{ __('footballProfiles.allMatches') }} <i class="bi bi-arrow-up-left" aria-hidden="true"></i></a></div>
                    @include('frontEnd.matches.partials.player-events', ['eventItems' => $recentEvents])
                </section>
                <div class="fp-column-stack">
                    <section class="fp-panel fp-facts"><div class="fp-heading"><h2>{{ __('footballProfiles.playerFacts') }}</h2></div><dl>
                        @if($countryName)<div><dt>{{ __('footballProfiles.country') }}</dt><dd>{{ $countryName }}</dd></div>@endif
                        @if($birthDate)<div><dt>{{ __('footballProfiles.born') }}</dt><dd>{{ $birthDate->translatedFormat('d F Y') }}</dd></div>@endif
                        @if($player->height)<div><dt>{{ __('footballProfiles.height') }}</dt><dd>{{ $player->height }} {{ __('footballProfiles.centimeters') }}</dd></div>@endif
                        @if($player->weight)<div><dt>{{ __('footballProfiles.weight') }}</dt><dd>{{ $player->weight }} {{ __('footballProfiles.kilograms') }}</dd></div>@endif
                    </dl></section>
                    @if($teams->isNotEmpty())<section class="fp-panel"><div class="fp-heading"><h2>{{ __('footballProfiles.currentTeams') }}</h2></div><div class="fp-clubs">@foreach($teams as $currentTeam) @include('frontEnd.matches.partials.player-team', ['affiliation' => $currentTeam]) @endforeach</div></section>@endif
                </div>
            </div>
            @if($news->isNotEmpty())<section class="fp-section"><div class="fp-heading"><h2>{{ __('footballProfiles.latestNews') }}</h2><a href="{{ $tabUrl('news') }}">{{ __('footballProfiles.allNews') }} <i class="bi bi-arrow-up-left" aria-hidden="true"></i></a></div>@include('frontEnd.matches.partials.news-grid', ['newsItems' => $news])</section>@endif
        @elseif($tab === 'matches')
            <div class="fp-heading"><h2>{{ __('footballProfiles.playerEvents') }}</h2></div>
            <div class="fp-panel">@include('frontEnd.matches.partials.player-events', ['eventItems' => $recentEvents])</div>
        @elseif($tab === 'career')
            <div class="fp-heading"><h2>{{ __('footballProfiles.teamHistory') }}</h2></div>
            @if($careerTeams->isNotEmpty())<div class="fp-clubs fp-clubs--grid">@foreach($careerTeams as $affiliation) @include('frontEnd.matches.partials.player-team') @endforeach</div>@else<p class="fp-empty">{{ __('footballProfiles.noCareer') }}</p>@endif
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
