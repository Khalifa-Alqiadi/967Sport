@extends('dashboard.layouts.master')
@section('title', __('backend.venues'))
@section('content')
    <div class="padding">
        <div class="box m-b-0">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe3c9;</i> {{ __('backend.edit') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> /
                    <a href="{{ route('venues') }}">{{ __('backend.venues') }}</a> /
                    <a>{{ $venue->name_ar ?: $venue->name_en }}</a>
                </small>
            </div>
        </div>
        <div class="box">
            <div class="box-body p-a-2">
                <form method="POST" action="{{ route('venues.update', ['id' => $venue->id]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('backend.name_ar') }}</label>
                                <input type="text" name="name_ar" value="{{ old('name_ar', $venue->name_ar) }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('backend.name_en') }}</label>
                                <input type="text" name="name_en" value="{{ old('name_en', $venue->name_en) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('backend.city') }}</label>
                                <input type="text" name="city_name" value="{{ old('city_name', $venue->city_name) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('backend.capacity') }}</label>
                                <input type="number" name="capacity" value="{{ old('capacity', $venue->capacity) }}" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('backend.surface') }}</label>
                                <input type="text" name="surface" value="{{ old('surface', $venue->surface) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('backend.address') }}</label>
                                <input type="text" name="address" value="{{ old('address', $venue->address) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('backend.photo') }}</label>
                                @if($venue->local_image)
                                    <div class="m-b-sm" id="current_img">
                                        <img src="{{ $venue->image_path }}" style="height:80px;border-radius:8px">
                                        <a onclick="document.getElementById('current_img').style.display='none';document.getElementById('image_delete').value='1';" class="btn btn-sm btn-default m-l-sm">{{ __('backend.delete') }}</a>
                                        <input type="hidden" name="image_delete" value="0" id="image_delete">
                                    </div>
                                @elseif($venue->api_image_path)
                                    <div class="m-b-sm">
                                        <img src="{{ $venue->api_image_path }}" style="height:60px;border-radius:8px;opacity:0.6">
                                        <small class="text-muted">{{ __('backend.api_source') }}</small>
                                    </div>
                                @endif
                                <input type="file" name="local_image" accept="image/*" class="form-control">
                                <small class="text-muted">{{ __('backend.upload_custom_logo_hint') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-t-md">
                        <button type="submit" class="btn btn-lg btn-primary">{{ __('backend.update') }}</button>
                        <a href="{{ route('venues') }}" class="btn btn-lg btn-default">{{ __('backend.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
