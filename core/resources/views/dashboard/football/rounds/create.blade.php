@php
    $name_var = 'name_' . @Helper::currentLanguage()->code;
@endphp
@extends('dashboard.layouts.master')
@section('title', __('backend.matches'))
@section('content')
    <div class="padding">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe02e;</i> {{ __('backend.add') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a>{!! $League->$name_var !!}</a> /
                    <a href="{{ route('leaguesRounds', ['league_id' => $League->id]) }}">{{ __('backend.matches') }}</a>
                </small>
            </div>
            <div class="box-body p-a-2">
                <form method="POST" action="{{ route('matchesStore', ['league_id' => $League->id]) }}" class="dashboard-form">
                    @csrf

                    <div class="form-group row">
                        <label for="season_id" class="col-sm-2 form-control-label">{{ __('backend.season') }}</label>
                        <div class="col-sm-10">
                            <select name="season_id" id="season_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}" required>
                                <option value="">{{ __('backend.season') }}</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>{{ $season->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="home_team_id" class="col-sm-2 form-control-label">{{ __('backend.home_team') }}</label>
                        <div class="col-sm-10">
                            <select name="home_team_id" id="home_team_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}" required>
                                <option value="">{{ __('backend.select') }}</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('home_team_id') == $team->id ? 'selected' : '' }}>{{ $team->{'name_' . @Helper::currentLanguage()->code} ?: $team->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="away_team_id" class="col-sm-2 form-control-label">{{ __('backend.away_team') }}</label>
                        <div class="col-sm-10">
                            <select name="away_team_id" id="away_team_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}" required>
                                <option value="">{{ __('backend.select') }}</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('away_team_id') == $team->id ? 'selected' : '' }}>{{ $team->{'name_' . @Helper::currentLanguage()->code} ?: $team->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="venue_id" class="col-sm-2 form-control-label">{{ __('backend.venue') }}</label>
                        <div class="col-sm-10">
                            <select name="venue_id" id="venue_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                                <option value="">{{ __('backend.select') }}</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="starting_at" class="col-sm-2 form-control-label">{{ __('backend.starting_at') }}</label>
                        <div class="col-sm-10">
                            <input type="datetime-local" name="starting_at" id="starting_at" value="{{ old('starting_at') }}" required class="form-control">
                        </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-lg btn-primary m-t">{{ __('backend.add') }}</button>
                            <a href="{{ route('leaguesRounds', ['league_id' => $League->id]) }}" class="btn btn-lg btn-default m-t">{{ __('backend.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
