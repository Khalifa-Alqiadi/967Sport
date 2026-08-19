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
                <h3><i class="material-icons">&#xe3c9;</i> {{ __('backend.edit') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('teams') }}">{{ __('backend.teams') }}</a> /
                    <a href="{{ route('teams.edit', ['id' => $team->id, 'tab' => 'players']) }}">{{ $team->$name_var ?: $team->name_en ?: $team->name_ar }}</a> /
                    <a>{{ $player->$name_var ?: $player->name_en ?: $player->name_ar }}</a>
                </small>
            </div>
        </div>

        <div class="box nav-active-border b-info">
            @include('dashboard.football.teams.tabs')
            <div class="tab-content clear b-t">
                <div class="tab-pane active">
                    <div class="box-body p-a-2">
                        <form method="POST" action="{{ route('teams.players.update', ['team' => $team->id, 'player' => $player->id]) }}" class="dashboard-form" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.name_ar') }}</label>
                                        <input type="text" name="name_ar" value="{{ old('name_ar', $player->name_ar) }}" class="form-control" required maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.name_en') }}</label>
                                        <input type="text" name="name_en" value="{{ old('name_en', $player->name_en) }}" class="form-control" maxlength="255">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.common_name') }}</label>
                                        <input type="text" name="common_name" value="{{ old('common_name', $player->common_name) }}" class="form-control" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.country') }}</label>
                                        <select name="country_id" id="country_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                                            <option value="">{{ __('backend.selectCountry') }}</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ (string) old('country_id', $player->country_id) === (string) $country->id ? 'selected' : '' }}>{{ $country->$title_var }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.photo') }}</label>
                                        @if($player->local_image)
                                            <div class="m-b-sm" id="current_photo">
                                                <img src="{{ $player->image_path }}" style="height:60px;width:60px;object-fit:cover;border-radius:50%">
                                                <a onclick="document.getElementById('current_photo').style.display='none';document.getElementById('photo_delete').value='1';" class="btn btn-sm btn-default m-l-sm">{{ __('backend.delete') }}</a>
                                                <input type="hidden" name="photo_delete" value="0" id="photo_delete">
                                            </div>
                                        @elseif($player->api_image_path)
                                            <div class="m-b-sm">
                                                <img src="{{ $player->api_image_path }}" style="height:50px;width:50px;object-fit:cover;border-radius:50%;opacity:0.6">
                                                <small class="text-muted">{{ __('backend.api_source') }}</small>
                                            </div>
                                        @endif
                                        <input type="file" name="local_image" accept="image/*" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.date_of_birth') }}</label>
                                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $player->date_of_birth) }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.gender') }}</label>
                                        <input type="text" name="gender" value="{{ old('gender', $player->gender) }}" class="form-control" maxlength="20">
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.height') }}</label>
                                        <input type="number" step="0.01" name="height" value="{{ old('height', $player->height) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.weight') }}</label>
                                        <input type="number" step="0.01" name="weight" value="{{ old('weight', $player->weight) }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.nationality_id') }}</label>
                                        <input type="number" name="nationality_id" value="{{ old('nationality_id', $player->nationality_id) }}" class="form-control" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.position_id') }}</label>
                                        <input type="number" name="position_id" value="{{ old('position_id', $player->position_id) }}" class="form-control" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.detailed_position_id') }}</label>
                                        <input type="number" name="detailed_position_id" value="{{ old('detailed_position_id', $player->detailed_position_id) }}" class="form-control" min="0">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.foot') }}</label>
                                        <input type="text" name="foot" value="{{ old('foot', $player->foot) }}" class="form-control" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.sport_id') }}</label>
                                        <input type="number" name="sport_id" value="{{ old('sport_id', $player->sport_id) }}" class="form-control" min="0">
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('backend.jersey_number') }}</label>
                                        <input type="number" name="jersey_number" value="{{ old('jersey_number', $player->pivot?->jersey_number) }}" class="form-control" min="0">
                                    </div>
                                </div>

                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.from_date') }}</label>
                                        <input type="date" name="from_date" value="{{ old('from_date', $player->pivot?->from_date) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.to_date') }}</label>
                                        <input type="date" name="to_date" value="{{ old('to_date', $player->pivot?->to_date) }}" class="form-control">
                                    </div>
                                </div> --}}

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.status') }}</label>
                                        <div class="radio">
                                            <label class="md-check">
                                                <input type="radio" name="is_current" value="1" class="has-value" {{ (int) old('is_current', (int) $player->pivot?->is_current) === 1 ? 'checked' : '' }}>
                                                <i class="primary"></i> {{ __('backend.active') }}
                                            </label>
                                            &nbsp; &nbsp;
                                            <label class="md-check">
                                                <input type="radio" name="is_current" value="0" class="has-value" {{ (int) old('is_current', (int) $player->pivot?->is_current) === 0 ? 'checked' : '' }}>
                                                <i class="danger"></i> {{ __('backend.notActive') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('backend.captain') }}</label>
                                        <div class="radio">
                                            <label class="md-check">
                                                <input type="radio" name="is_captain" value="1" class="has-value" {{ (int) old('is_captain', (int) $player->pivot?->is_captain) === 1 ? 'checked' : '' }}>
                                                <i class="primary"></i> {{ __('backend.yes') }}
                                            </label>
                                            &nbsp; &nbsp;
                                            <label class="md-check">
                                                <input type="radio" name="is_captain" value="0" class="has-value" {{ (int) old('is_captain', (int) $player->pivot?->is_captain) === 0 ? 'checked' : '' }}>
                                                <i class="danger"></i> {{ __('backend.no') }}
                                            </label>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>

                            <div class="form-group row m-t-md">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-lg btn-primary m-t">{{ __('backend.update') }}</button>
                                    <a href="{{ route('teams.edit', ['id' => $team->id, 'tab' => 'players']) }}" class="btn btn-lg btn-default m-t">{{ __('backend.cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
