@extends('frontEnd.layouts.master')

@section('bodyClass', 'sport-matches-page sport-competitions-page')

@section('content')
<div class="sm-page">
    <section class="sm-hero sm-hero--competitions">
        <div class="container sm-hero__inner">
            <div>
                <span class="sm-eyebrow"><i></i>{{ __('matches.yemeniFootball') }}</span>
                <h1>{{ __('matches.allCompetitions') }}</h1>
                <p>{{ __('matches.competitionsIntro') }}</p>
            </div>
            <div class="sm-hero-stat">
                <strong>{{ str_pad($leagues->count(), 2, '0', STR_PAD_LEFT) }}</strong>
                <span>{{ __('matches.activeCompetition') }}</span>
            </div>
        </div>
    </section>

    <section class="sm-section">
        <div class="container">
            <div class="sm-section-heading">
                <div><span>{{ __('matches.explore') }}</span><h2>{{ __('matches.chooseCompetition') }}</h2></div>
                <a href="{{ route('sport.matches') }}">{{ __('matches.allMatches') }} <i class="bi bi-arrow-up-left"></i></a>
            </div>
            @if($leagues->isNotEmpty())
                <div class="sm-competitions-grid">
                    @foreach($leagues as $index => $league)
                        @php
                            $isEnglish = app()->getLocale() === 'en';
                            $leagueName = $isEnglish ? ($league->name_en ?: $league->name_ar) : ($league->name_ar ?: $league->name_en);
                            $currentSeason = $league->seasons->firstWhere('id', $league->current_season_id) ?: $league->seasons->firstWhere('is_current', true) ?: $league->seasons->first();
                        @endphp
                        <a class="sm-competition-card" href="{{ route('sport.league', $league->id) }}">
                            <span class="sm-competition-card__no">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="sm-competition-card__logo">
                                @if($league->image_path)<img src="{{ $league->image_path }}" alt="{{ $leagueName }}" loading="lazy">@else<i class="bi bi-trophy"></i>@endif
                            </div>
                            <div class="sm-competition-card__body">
                                <span>{{ $isEnglish ? ($league->country?->title_en ?: $league->country?->title_ar ?: __('matches.yemen')) : ($league->country?->title_ar ?: $league->country?->title_en ?: __('matches.yemen')) }}</span>
                                <h2>{{ $leagueName }}</h2>
                                <p>{{ $currentSeason?->name ?: __('matches.currentSeason') }}</p>
                            </div>
                            <div class="sm-competition-card__stats">
                                <span><b>{{ $league->matches_count }}</b>{{ __('matches.match') }}</span>
                                <span><b>{{ $league->seasons_count }}</b>{{ __('matches.season') }}</span>
                                <i class="bi bi-arrow-up-left"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                @include('frontEnd.matches.partials.empty', ['title' => __('matches.noCompetitions'), 'description' => __('matches.noCompetitionsDescription')])
            @endif
        </div>
    </section>
</div>
@endsection

@push('after-styles')
<link href="{{ URL::asset('assets/frontend/css/967sport-matches.css') }}?v={{ Helper::system_version() }}" rel="stylesheet">
@endpush
