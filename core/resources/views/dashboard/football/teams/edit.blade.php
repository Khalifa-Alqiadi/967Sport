@php
    $title_var = 'title_' . @Helper::currentLanguage()->code;
    $title_var2 = 'title_' . config('smartend.default_language');
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
                    <a>{{ __('backend.teams') }}</a>
                </small>
            </div>
        </div>

        <div class="box nav-active-border b-info">
            @include('dashboard.football.teams.tabs')
            <div class="tab-content clear b-t">
                <div class="tab-pane {{ $tab == 'details' ? 'active' : '' }}" id="tab_details">
                    <div class="box-body p-a-2">
                        <form method="POST" action="{{ route('teamsUpdate',['id'=>$team->id]) }}" class="dashboard-form" enctype="multipart/form-data">
                            @csrf

                            @foreach(Helper::languagesList() as $ActiveLanguage)
                                @if($ActiveLanguage->box_status)
                                    <div class="form-group row">
                                        <label for="name_{{ @$ActiveLanguage->code }}"
                                               class="col-sm-2 form-control-label">{!!  __('backend.name') !!} {!! @Helper::languageName($ActiveLanguage) !!}
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="text" autocomplete="off" name="name_{{ @$ActiveLanguage->code }}" id="name_{{ @$ActiveLanguage->code }}" value="{{ $team->{'name_'.@$ActiveLanguage->code} }}" required maxlength="191" dir="{{ @$ActiveLanguage->direction }}" class="form-control"/>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <div class="form-group row">
                                <label for="country" class="col-sm-2 form-control-label">{{ __('backend.country') }}</label>
                                <div class="col-sm-10">
                                    <select name="country_id" id="country_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                                        <option value="">{{__('backend.selectCountry')}}</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}" {{$team->country_id == $country->id ? 'selected' : ''}}>{{$country->$title_var}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="local_image" class="col-sm-2 form-control-label">{{ __('backend.logo') }}</label>
                                <div class="col-sm-10">
                                    @if($team->local_image)
                                        <div class="row m-b-sm">
                                            <div class="col-sm-4 box p-a-xs" id="current_logo">
                                                <img src="{{ $team->image_path }}" class="img-responsive" style="max-height:80px">
                                                <br>
                                                <a onclick="document.getElementById('current_logo').style.display='none';document.getElementById('logo_delete').value='1';" class="btn btn-sm btn-default">{{ __('backend.delete') }}</a>
                                                <input type="hidden" name="logo_delete" value="0" id="logo_delete">
                                            </div>
                                        </div>
                                    @elseif($team->api_image_path)
                                        <div class="row m-b-sm">
                                            <div class="col-sm-4 p-a-xs">
                                                <img src="{{ $team->api_image_path }}" class="img-responsive" style="max-height:80px;opacity:0.6">
                                                <small class="text-muted d-block">{{ __('backend.api_source') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="local_image" accept="image/*" class="form-control">
                                    <small class="text-muted">{{ __('backend.upload_custom_logo_hint') }}</small>
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                <label for="major_competitions1" class="col-sm-2 form-control-label">{{ __('backend.major_competitions') }}</label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label class="md-check">
                                            <input type="radio" name="major_competitions" value="1" class="has-value" {{ ($team->major_competitions==1)?"checked":"" }} id="major_competitions1">
                                            <i class="primary"></i>
                                            {{ __('backend.active') }}
                                        </label>
                                        &nbsp; &nbsp;
                                        <label class="md-check">
                                            <input type="radio" name="major_competitions" value="0" class="has-value" {{ ($team->major_competitions==0)?"checked":"" }} id="major_competitions2">
                                            <i class="danger"></i>
                                            {{ __('backend.notActive') }}
                                        </label>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="form-group row">
                                <label for="major_national_teams1" class="col-sm-2 form-control-label">{{ __('backend.major_national_teams') }}</label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label class="md-check">
                                            <input type="radio" name="major_national_teams" value="1" class="has-value" {{ ($team->major_national_teams==1)?"checked":"" }} id="major_national_teams1">
                                            <i class="primary"></i>
                                            {{ __('backend.active') }}
                                        </label>
                                        &nbsp; &nbsp;
                                        <label class="md-check">
                                            <input type="radio" name="major_national_teams" value="0" class="has-value" {{ ($team->major_national_teams==0)?"checked":"" }} id="major_national_teams2">
                                            <i class="danger"></i>
                                            {{ __('backend.notActive') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="is_home1" class="col-sm-2 form-control-label">{{ __('backend.show_matches_on_homepage') }}</label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label class="md-check">
                                            <input type="radio" name="is_home" value="1" class="has-value" {{ ($team->is_home==1)?"checked":"" }} id="is_home1">
                                            <i class="primary"></i>
                                            {{ __('backend.active') }}
                                        </label>
                                        &nbsp; &nbsp;
                                        <label class="md-check">
                                            <input type="radio" name="is_home" value="0" class="has-value" {{ ($team->is_home==0)?"checked":"" }} id="is_home2">
                                            <i class="danger"></i>
                                            {{ __('backend.notActive') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status1" class="col-sm-2 form-control-label">{{ __('backend.status') }}</label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label class="md-check">
                                            <input type="radio" name="status" value="1" class="has-value" {{ ($team->status==1)?"checked":"" }} id="status1">
                                            <i class="primary"></i>
                                            {{ __('backend.active') }}
                                        </label>
                                        &nbsp; &nbsp;
                                        <label class="md-check">
                                            <input type="radio" name="status" value="0" class="has-value" {{ ($team->status==0)?"checked":"" }} id="status2">
                                            <i class="danger"></i>
                                            {{ __('backend.notActive') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row m-t-md">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-lg btn-primary m-t">{{ __('backend.update') }}</button>
                                    <a href="{{ route('teams') }}" class="btn btn-lg btn-default m-t">{{ __('backend.cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane {{ $tab == 'players' ? 'active' : '' }}" id="tab_players">
                    <div class="box-body p-a-2">
                        @if($players->count() == 0)
                            <div class="p-a text-center">
                                <div class="text-muted m-b"><i class="fa fa-users fa-4x"></i></div>
                                <h6>{{ __('backend.noData') }}</h6>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered m-a-0">
                                    <thead class="dker">
                                        <tr>
                                            <th class="text-center" style="width:80px">{{ __('backend.id') }}</th>
                                            <th>{{ __('backend.name') }}</th>
                                            <th class="text-center" style="width:220px">{{ __('backend.country') }}</th>
                                            <th class="text-center" style="width:120px">{{ __('backend.jersey_number') }}</th>
                                            <th class="text-center" style="width:120px">{{ __('backend.status') }}</th>
                                            <th class="text-center" style="width:150px">{{ __('backend.options') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($players as $player)
                                            <tr id="player-row-{{ $player->id }}">
                                                <td class="text-center">{{ $player->id }}</td>
                                                <td class="h6 nowrap">
                                                    <a href="{{ route('teams.players.edit', ['team' => $team->id, 'player' => $player->id]) }}">
                                                        @if($player->image_path)
                                                            <img src="{{ $player->image_path }}" style="height:34px;width:34px;object-fit:cover;border-radius:50%;margin-inline-end:8px" alt="">
                                                        @endif
                                                        {{ $player->{'name_'.@Helper::currentLanguage()->code} ?: $player->name_en ?: $player->name_ar }}
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ $player->country?->$title_var ?? '-' }}</td>
                                                <td class="text-center">{{ $player->pivot?->jersey_number ?? '-' }}</td>
                                                <td class="text-center">
                                                    <i class="fa {{ $player->pivot?->is_current ? 'fa-check text-success' : 'fa-times text-danger' }} inline"></i>
                                                </td>
                                                <td class="text-center">
                                                    <div class="dropdown {{ $loop->remaining < 2 ? 'dropup' : '' }}">
                                                        <button type="button" class="btn btn-sm light dk dropdown-toggle" data-toggle="dropdown">
                                                            <i class="material-icons">&#xe5d4;</i> {{ __('backend.options') }}
                                                        </button>
                                                        <div class="dropdown-menu pull-right">
                                                            @if(@Auth::user()->permissionsGroup->edit_status)
                                                                <a class="dropdown-item" href="{{ route('teams.players.edit', ['team' => $team->id, 'player' => $player->id]) }}">
                                                                    <i class="material-icons">&#xe3c9;</i> {{ __('backend.edit') }}
                                                                </a>
                                                            @endif
                                                            @if(@Auth::user()->permissionsGroup->delete_status)
                                                                <a class="dropdown-item text-danger" onclick="DeletePlayer('{{ $player->id }}')">
                                                                    <i class="material-icons">&#xe872;</i> {{ __('backend.delete') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="delete-player" class="modal fade" data-backdrop="true">
        <div class="modal-dialog" id="animate-player-delete">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                </div>
                <div class="modal-body text-center p-lg">
                    <h5 class="m-b-0">
                        {{ __('backend.confirmationDeleteMsg') }}
                    </h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">{{ __('backend.no') }}</button>
                    <button type="button" id="player_delete_btn" row-id="" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script type="text/javascript">
        function DeletePlayer(id) {
            $("#player_delete_btn").attr("row-id", id);
            $("#delete-player").modal("show");
        }

        $("#player_delete_btn").click(function () {
            var button = $(this);
            var row_id = button.attr('row-id');
            var deleteUrl = "{{ route('teams.players.destroy', ['team' => $team->id, 'player' => '__PLAYER__']) }}";

            button.html("<img src=\"{{ asset('assets/dashboard/images/loading.gif') }}\" style=\"height: 25px\"/> {!! __('backend.yes') !!}");

            if (row_id != "") {
                $.ajax({
                    type: "GET",
                    url: deleteUrl.replace('__PLAYER__', row_id),
                    success: function (result) {
                        if (result.stat == 'success') {
                            $('#player-row-' + row_id).remove();
                            swal({
                                title: "<span class='text-success'>{{ __('backend.deleteDone') }}</span>",
                                text: "",
                                html: true,
                                type: "success",
                                confirmButtonText: "{{ __('backend.close') }}",
                                confirmButtonColor: "#acacac",
                                timer: 5000,
                            });
                        }

                        button.html("{!! __('backend.yes') !!}");
                        $('#delete-player').modal('hide');
                        $('.modal-backdrop').hide();
                    }
                });
            }
        });
    </script>
@endpush
