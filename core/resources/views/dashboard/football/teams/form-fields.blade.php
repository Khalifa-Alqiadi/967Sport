@php($editing = isset($team))
<div class="row">
    <div class="col-md-8">
        <div class="box b-a m-b-md">
            <div class="box-header"><h6 class="m-a-0">{{ __('backend.team_identity') }}</h6></div>
            <div class="box-body">
                <div class="row">
                    @foreach(Helper::languagesList() as $ActiveLanguage)
                        @if($ActiveLanguage->box_status)
                            <div class="col-md-6"><div class="form-group">
                                <label>{{ __('backend.name') }} {!! Helper::languageName($ActiveLanguage) !!}</label>
                                <input type="text" name="name_{{ $ActiveLanguage->code }}" value="{{ old('name_'.$ActiveLanguage->code, $editing ? $team->{'name_'.$ActiveLanguage->code} : '') }}" class="form-control" maxlength="255" dir="{{ $ActiveLanguage->direction }}" {{ $ActiveLanguage->code == config('smartend.default_language') ? 'required' : '' }}>
                            </div></div>
                        @endif
                    @endforeach
                    <div class="col-md-4"><div class="form-group"><label>{{ __('backend.short_code') }}</label><input name="short_code" value="{{ old('short_code', $editing ? $team->short_code : '') }}" class="form-control" maxlength="50"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>{{ __('backend.founded') }}</label><input type="number" name="founded" value="{{ old('founded', $editing ? $team->founded : '') }}" class="form-control" min="1800" max="{{ now()->year + 1 }}"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>{{ __('backend.team_type') }}</label><input name="type" value="{{ old('type', $editing ? $team->type : '') }}" class="form-control" maxlength="50" list="team-types"><datalist id="team-types"><option value="club"><option value="national"><option value="youth"><option value="women"></datalist></div></div>
                </div>
            </div>
        </div>
        <div class="box b-a m-b-md">
            <div class="box-header"><h6 class="m-a-0">{{ __('backend.team_sport_data') }}</h6></div>
            <div class="box-body"><div class="row">
                <div class="col-md-6"><div class="form-group"><label>{{ __('backend.country') }}</label><select name="country_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}" required><option value="">{{ __('backend.selectCountry') }}</option>@foreach($countries as $country)<option value="{{ $country->id }}" {{ (string) old('country_id', $editing ? $team->country_id : '') === (string) $country->id ? 'selected' : '' }}>{{ $country->$title_var }}</option>@endforeach</select></div></div>
                <div class="col-md-6"><div class="form-group"><label>{{ __('backend.venue') }}</label><select name="venue_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}"><option value="">{{ __('backend.without_venue') }}</option>@foreach($venues as $venue)<option value="{{ $venue->id }}" {{ (string) old('venue_id', $editing ? $team->venue_id : '') === (string) $venue->id ? 'selected' : '' }}>{{ $venue->$name_var ?? $venue->name_en ?? $venue->name_ar }}</option>@endforeach</select></div></div>
                <div class="col-md-6"><div class="form-group"><label>{{ __('backend.sport_id') }}</label><input type="number" name="sport_id" value="{{ old('sport_id', $editing ? $team->sport_id : 1) }}" class="form-control" min="1"></div></div>
                <div class="col-md-6"><div class="form-group"><label>{{ __('backend.ordering') }}</label><input type="number" name="row_no" value="{{ old('row_no', $editing ? $team->row_no : '') }}" class="form-control" min="0"><small class="text-muted">{{ __('backend.auto_order_hint') }}</small></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box b-a m-b-md"><div class="box-header"><h6 class="m-a-0">{{ __('backend.team_branding') }}</h6></div><div class="box-body">
            @if($editing && $team->image_path)<div class="text-center m-b"><img src="{{ $team->image_path }}" id="current_logo" style="height:100px;max-width:160px;object-fit:contain"><div class="m-t-sm">@if($team->local_image)<button type="button" class="btn btn-sm btn-default" onclick="document.getElementById('current_logo').style.display='none';document.getElementById('logo_delete').value='1'">{{ __('backend.delete') }}</button><input type="hidden" name="logo_delete" value="0" id="logo_delete">@endif</div></div>@endif
            <div class="form-group"><label>{{ __('backend.logo') }}</label><input type="file" name="local_image" accept="image/*" class="form-control"><small class="text-muted">{{ __('backend.upload_custom_logo_hint') }}</small></div>
            <div class="form-group"><label>{{ __('backend.image_url') }}</label><input type="url" name="image_path" value="{{ old('image_path', $editing ? $team->api_image_path : '') }}" class="form-control" dir="ltr"></div>
        </div></div>
        <div class="box b-a"><div class="box-header"><h6 class="m-a-0">{{ __('backend.visibility_settings') }}</h6></div><div class="box-body">
            @foreach(['status' => 'status', 'placeholder' => 'placeholder', 'major_competitions' => 'major_competitions', 'major_national_teams' => 'major_national_teams', 'is_home' => 'show_team_on_homepage'] as $field => $label)
                @php($value = (int) old($field, $editing ? $team->$field : ($field === 'status' ? 1 : 0)))
                <div class="form-group"><label class="d-block">{{ __('backend.'.$label) }}</label><label class="md-check m-r"><input type="radio" name="{{ $field }}" value="1" {{ $value === 1 ? 'checked' : '' }} required><i class="primary"></i> {{ __('backend.yes') }}</label><label class="md-check"><input type="radio" name="{{ $field }}" value="0" {{ $value === 0 ? 'checked' : '' }} required><i class="danger"></i> {{ __('backend.no') }}</label></div>
            @endforeach
        </div></div>
    </div>
</div>
