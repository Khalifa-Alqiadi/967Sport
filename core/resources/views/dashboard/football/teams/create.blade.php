@php
    $title_var = 'title_' . @Helper::currentLanguage()->code;
    $name_var = 'name_' . @Helper::currentLanguage()->code;
@endphp
@extends('dashboard.layouts.master')
@section('title', __('backend.teams'))
@section('content')
    <div class="padding">
        <div class="box m-b-0">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe02e;</i> {{ __('backend.add') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('teams') }}">{{ __('backend.teams') }}</a>
                </small>
            </div>
            <div class="box-body p-a-2">
                <form method="POST" action="{{ route('teamsStore') }}" class="dashboard-form" enctype="multipart/form-data">
                    @csrf

                    @include('dashboard.football.teams.form-fields')

                    <div class="form-group row m-t-md">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-lg btn-primary m-t">{{ __('backend.add') }}</button>
                            <a href="{{ route('teams') }}" class="btn btn-lg btn-default m-t">{{ __('backend.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
