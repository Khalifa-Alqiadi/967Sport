@php
    $name_var = 'name_' . @Helper::currentLanguage()->code;
@endphp
@extends('dashboard.layouts.master')
@section('content')
    <div class="padding">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe02e;</i> {{ __('backend.add') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('seasons', ['league_id' => $League->id]) }}">{!! $League->$name_var !!}</a> /
                    <a>{{ __('backend.seasons') }}</a>
                </small>
            </div>
            <div class="box-body p-a-2">
                <form method="POST" action="{{ route('seasonsStore', ['league_id' => $League->id]) }}" class="dashboard-form">
                    @csrf

                    <div class="form-group row">
                        <label for="name" class="col-sm-2 form-control-label">{{ __('backend.season') }}</label>
                        <div class="col-sm-10">
                            <input type="text" autocomplete="off" name="name" id="name" value="{{ old('name') }}" required maxlength="191" class="form-control" placeholder="2025/2026">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="starting_at" class="col-sm-2 form-control-label">{{ __('backend.starting_at') }}</label>
                        <div class="col-sm-10">
                            <input type="date" name="starting_at" id="starting_at" value="{{ old('starting_at') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="ending_at" class="col-sm-2 form-control-label">{{ __('backend.ending_at') }}</label>
                        <div class="col-sm-10">
                            <input type="date" name="ending_at" id="ending_at" value="{{ old('ending_at') }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="is_current1" class="col-sm-2 form-control-label">{{ __('backend.is_current') }}</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label class="md-check">
                                    <input type="radio" name="is_current" value="1" class="has-value" id="is_current1">
                                    <i class="primary"></i>
                                    {{ __('backend.active') }}
                                </label>
                                &nbsp; &nbsp;
                                <label class="md-check">
                                    <input type="radio" name="is_current" value="0" class="has-value" checked id="is_current2">
                                    <i class="danger"></i>
                                    {{ __('backend.notActive') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-lg btn-primary m-t">{{ __('backend.add') }}</button>
                            <a href="{{ route('seasons', ['league_id' => $League->id]) }}" class="btn btn-lg btn-default m-t">{{ __('backend.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
