@extends('dashboard.layouts.master')
@section('title', __('backend.leagues'))
@section('content')
    <div class="padding">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe02e;</i> {{ __('backend.add') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('leagues') }}">{{ __('backend.leagues') }}</a>
                </small>
            </div>
            <div class="box-body p-a-2">
                <form method="POST" action="{{ route('leaguesStore') }}" class="dashboard-form" enctype="multipart/form-data">
                    @csrf

                    @foreach(Helper::languagesList() as $ActiveLanguage)
                        @if($ActiveLanguage->box_status)
                            <div class="form-group row">
                                <label for="name_{{ @$ActiveLanguage->code }}" class="col-sm-2 form-control-label">
                                    {!! __('backend.name') !!} {!! @Helper::languageName($ActiveLanguage) !!}
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" autocomplete="off" name="name_{{ @$ActiveLanguage->code }}" id="name_{{ @$ActiveLanguage->code }}" value="{{ old('name_' . @$ActiveLanguage->code) }}" {{ @$ActiveLanguage->code == config('smartend.default_language') ? 'required' : '' }} maxlength="191" dir="{{ @$ActiveLanguage->direction }}" class="form-control"/>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="form-group row">
                        <label for="country_id" class="col-sm-2 form-control-label">{{ __('backend.country') }}</label>
                        <div class="col-sm-10">
                            <select name="country_id" id="country_id" class="form-control c-select">
                                <option value="">{{ __('backend.selectCountry') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->{'title_' . @Helper::currentLanguage()->code} }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="logo" class="col-sm-2 form-control-label">{{ __('backend.logo') }}</label>
                        <div class="col-sm-10">
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-lg btn-primary m-t">{{ __('backend.add') }}</button>
                            <a href="{{ route('leagues') }}" class="btn btn-lg btn-default m-t">{{ __('backend.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
