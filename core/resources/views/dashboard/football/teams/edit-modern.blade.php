@php
    $title_var = 'title_' . Helper::currentLanguage()->code;
    $name_var = 'name_' . Helper::currentLanguage()->code;
@endphp
@extends('dashboard.layouts.master')
@section('title', __('backend.teams'))
@section('content')
<div class="padding">
    <div class="box m-b-0"><div class="box-header dker"><h3><i class="material-icons">&#xe3c9;</i> {{ __('backend.edit') }}</h3><small><a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> / <a href="{{ route('teams') }}">{{ __('backend.teams') }}</a> / {{ $team->$name_var ?: $team->name_en ?: $team->name_ar }}</small></div></div>
    <div class="box nav-active-border b-info">
        @include('dashboard.football.teams.tabs')
        <div class="tab-content clear b-t">
            <div class="tab-pane {{ $tab === 'details' ? 'active' : '' }}" id="tab_details"><div class="box-body p-a-2">
                <form method="POST" action="{{ route('teamsUpdate', ['id' => $team->id]) }}" class="dashboard-form" enctype="multipart/form-data">@csrf @include('dashboard.football.teams.form-fields')<div class="m-t-md"><button type="submit" class="btn btn-lg btn-primary">{{ __('backend.update') }}</button> <a href="{{ route('teams') }}" class="btn btn-lg btn-default">{{ __('backend.cancel') }}</a></div></form>
            </div></div>
            <div class="tab-pane {{ $tab === 'players' ? 'active' : '' }}" id="tab_players"><div class="box-body p-a-2">
                <div class="clearfix m-b-md"><div class="pull-left"><h5 class="m-a-0">{{ __('backend.team_players') }}</h5><small class="text-muted">{{ __('backend.team_players_hint') }}</small></div>@if(@Auth::user()->permissionsGroup->edit_status)<a href="{{ route('teams.players.create', ['team' => $team->id]) }}" class="btn btn-primary pull-right"><i class="material-icons">&#xe7fe;</i> {{ __('backend.add_player') }}</a>@endif</div>
                @if($players->isEmpty())
                    <div class="p-a text-center"><div class="text-muted m-b"><i class="fa fa-users fa-4x"></i></div><h6>{{ __('backend.noData') }}</h6><a href="{{ route('teams.players.create', ['team' => $team->id]) }}" class="btn btn-primary m-t">{{ __('backend.add_player') }}</a></div>
                @else
                <div class="table-responsive"><table class="table table-bordered m-a-0"><thead class="dker"><tr><th class="text-center">{{ __('backend.id') }}</th><th>{{ __('backend.name') }}</th><th class="text-center">{{ __('backend.country') }}</th><th class="text-center">{{ __('backend.season') }}</th><th class="text-center">{{ __('backend.jersey_number') }}</th><th class="text-center">{{ __('backend.captain') }}</th><th class="text-center">{{ __('backend.status') }}</th><th class="text-center">{{ __('backend.options') }}</th></tr></thead><tbody>
                    @foreach($players as $player)<tr id="player-row-{{ $player->id }}"><td class="text-center">{{ $player->id }}</td><td class="h6 nowrap"><a href="{{ route('teams.players.edit', ['team'=>$team->id,'player'=>$player->id]) }}">@if($player->image_path)<img src="{{ $player->image_path }}" style="height:34px;width:34px;object-fit:cover;border-radius:50%;margin-inline-end:8px">@endif {{ $player->$name_var ?: $player->name_en ?: $player->name_ar }}</a></td><td class="text-center">{{ $player->country?->$title_var ?? '-' }}</td><td class="text-center">{{ $player->pivot?->season_id ?? '-' }}</td><td class="text-center">{{ $player->pivot?->jersey_number ?? '-' }}</td><td class="text-center"><i class="fa {{ $player->pivot?->is_captain ? 'fa-star text-warning' : 'fa-minus text-muted' }}"></i></td><td class="text-center"><i class="fa {{ $player->pivot?->is_current ? 'fa-check text-success' : 'fa-times text-danger' }}"></i></td><td class="text-center"><a class="btn btn-sm btn-default" href="{{ route('teams.players.edit', ['team'=>$team->id,'player'=>$player->id]) }}"><i class="material-icons">&#xe3c9;</i></a> <button type="button" class="btn btn-sm btn-danger" onclick="DeletePlayer('{{ $player->id }}')"><i class="material-icons">&#xe872;</i></button></td></tr>@endforeach
                </tbody></table></div>
                @endif
            </div></div>
        </div>
    </div>
</div>
<div id="delete-player" class="modal fade" data-backdrop="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5>{{ __('backend.confirmation') }}</h5></div><div class="modal-body text-center p-lg">{{ __('backend.confirmationDeleteMsg') }}</div><div class="modal-footer"><button class="btn dark-white" data-dismiss="modal">{{ __('backend.no') }}</button><button id="player_delete_btn" row-id="" class="btn danger">{{ __('backend.yes') }}</button></div></div></div></div>
@endsection
@push('after-scripts')
<script>function DeletePlayer(id){$('#player_delete_btn').attr('row-id',id);$('#delete-player').modal('show')}$('#player_delete_btn').click(function(){var b=$(this),id=b.attr('row-id'),u="{{ route('teams.players.destroy',['team'=>$team->id,'player'=>'__PLAYER__']) }}";if(!id)return;b.prop('disabled',true);$.get(u.replace('__PLAYER__',id),function(r){if(r.stat==='success'){$('#player-row-'+id).remove();swal({title:"<span class='text-success'>{{ __('backend.deleteDone') }}</span>",html:true,type:'success',confirmButtonText:"{{ __('backend.close') }}",timer:5000})}$('#delete-player').modal('hide')}).always(function(){b.prop('disabled',false)})});</script>
@endpush
