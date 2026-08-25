@php
    $nameVar = 'name_' . @Helper::currentLanguage()->code;
    $fallbackNameVar = 'name_' . config('smartend.default_language');
    $homeName = $match->homeTeam?->{$nameVar} ?: $match->homeTeam?->{$fallbackNameVar} ?: '-';
    $awayName = $match->awayTeam?->{$nameVar} ?: $match->awayTeam?->{$fallbackNameVar} ?: '-';
    $leagueName = $match->league?->{$nameVar} ?: $match->league?->{$fallbackNameVar} ?: '-';
    $scorePhases = [
        ['label' => __('backend.halfTimeScore'), 'home' => 'ht_home_score', 'away' => 'ht_away_score', 'code' => 'HT'],
        ['label' => __('backend.fullTimeScore'), 'home' => 'ft_home_score', 'away' => 'ft_away_score', 'code' => 'FT'],
        ['label' => __('backend.extraTimeScore'), 'home' => 'et_home_score', 'away' => 'et_away_score', 'code' => 'ET'],
        ['label' => __('backend.penaltyScore'), 'home' => 'pen_home', 'away' => 'pen_away', 'code' => 'PEN'],
    ];
    $goalEvents = $match->events
        ->whereIn('type', [\App\Models\FixtureEvent::TYPE_GOAL, \App\Models\FixtureEvent::TYPE_OWN_GOAL, \App\Models\FixtureEvent::TYPE_PENALTY])
        ->sortBy('sort_order');
    $playerName = function ($player) use ($nameVar, $fallbackNameVar) {
        return $player?->{$nameVar} ?: $player?->{$fallbackNameVar} ?: $player?->common_name ?: '-';
    };
@endphp

@extends('dashboard.layouts.master')
@section('title', __('backend.matchControl'))

@section('content')
    <div class="padding match-control-page">
        <div class="box m-b-0">
            <div class="box-header dker match-page-heading">
                <div>
                    <h3><i class="material-icons">sports_soccer</i> {{ __('backend.matchControl') }}</h3>
                    <small>{{ __('backend.matchControlHint') }}</small>
                </div>
                <a class="btn white b-a" href="{{ route('leaguesRounds', ['league_id' => $match->league_id, 'season_id' => $match->season_id]) }}">
                    <i class="material-icons md-18">arrow_back</i> {{ __('backend.back') }}
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger m-t"><ul class="m-b-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('matchUpdate', ['id' => $match->id]) }}" class="dashboard-form">
            @csrf

            <section class="match-scoreboard">
                <div class="match-scoreboard-meta">
                    <span>{{ $leagueName }}</span>
                    <strong id="match-status-preview">{{ $matchStatuses[$match->state_code] ?? $match->state_name ?? __('backend.matchStatusNotStarted') }}</strong>
                    <small>{{ $match->season?->name }} @if($match->round?->name) · {{ $match->round->name }} @endif</small>
                </div>
                <div class="match-scoreboard-body">
                    <div class="match-scoreboard-team">
                        <div class="match-team-logo">@if($match->homeTeam?->image_path)<img src="{{ $match->homeTeam->image_path }}" alt="{{ $homeName }}">@else<span>{{ mb_substr($homeName, 0, 1) }}</span>@endif</div>
                        <b>{{ $homeName }}</b><small>{{ __('backend.home_team') }}</small>
                    </div>
                    <div class="match-scoreboard-result">
                        <input type="number" name="home_score" min="0" max="99" value="{{ old('home_score', $match->home_score) }}" aria-label="{{ __('backend.homeGoals') }}" @readonly($goalEvents->isNotEmpty())>
                        <em>:</em>
                        <input type="number" name="away_score" min="0" max="99" value="{{ old('away_score', $match->away_score) }}" aria-label="{{ __('backend.awayGoals') }}" @readonly($goalEvents->isNotEmpty())>
                        <span id="scoreboard-minute">@if($match->minute){{ $match->minute }}′@else VS @endif</span>
                    </div>
                    <div class="match-scoreboard-team">
                        <div class="match-team-logo">@if($match->awayTeam?->image_path)<img src="{{ $match->awayTeam->image_path }}" alt="{{ $awayName }}">@else<span>{{ mb_substr($awayName, 0, 1) }}</span>@endif</div>
                        <b>{{ $awayName }}</b><small>{{ __('backend.away_team') }}</small>
                    </div>
                </div>
            </section>

            <div class="match-control-grid">
                <section class="match-control-card">
                    <header><span class="match-card-icon"><i class="material-icons">tune</i></span><div><h4>{{ __('backend.matchStatus') }}</h4><small>{{ __('backend.matchSchedule') }}</small></div></header>
                    <div class="match-card-body">
                        <div class="form-group">
                            <label for="state_code">{{ __('backend.matchStatus') }}</label>
                            <select name="state_code" id="state_code" class="form-control select2" required>
                                @foreach($matchStatuses as $statusCode => $statusLabel)<option value="{{ $statusCode }}" @selected(old('state_code', $match->state_code ?: 'NS') === $statusCode)>{{ $statusLabel }}</option>@endforeach
                            </select>
                        </div>
                        <div class="form-group m-b-0"><label for="starting_at">{{ __('backend.matchSchedule') }}</label><input type="datetime-local" name="starting_at" id="starting_at" class="form-control" value="{{ old('starting_at', $match->starting_at?->format('Y-m-d\TH:i')) }}" required></div>
                    </div>
                </section>

                <section class="match-control-card">
                    <header><span class="match-card-icon"><i class="material-icons">timer</i></span><div><h4>{{ __('backend.matchTiming') }}</h4><small>{{ __('backend.minutesShort') }}</small></div></header>
                    <div class="match-card-body match-time-fields">
                        <label><span>{{ __('backend.matchMinute') }}</span><div><input type="number" name="minute" id="match_minute" min="0" max="180" value="{{ old('minute', $match->minute) }}"><small>′</small></div></label>
                        <label><span>{{ __('backend.firstHalfAddedTime') }}</span><div><input type="number" name="first_half_added_time" min="0" max="60" value="{{ old('first_half_added_time', $match->first_half_added_time) }}"><small>+</small></div></label>
                        <label><span>{{ __('backend.secondHalfAddedTime') }}</span><div><input type="number" name="second_half_added_time" min="0" max="60" value="{{ old('second_half_added_time', $match->second_half_added_time) }}"><small>+</small></div></label>
                    </div>
                </section>
            </div>

            <section class="match-control-card">
                <header><span class="match-card-icon"><i class="material-icons">scoreboard</i></span><div><h4>{{ __('backend.scoreManagement') }}</h4><small>{{ $homeName }} × {{ $awayName }}</small></div></header>
                <div class="match-card-body"><div class="match-phase-grid">
                    @foreach($scorePhases as $phase)
                        <div class="match-phase-card">
                            <div class="match-phase-title"><span>{{ $phase['code'] }}</span><b>{{ $phase['label'] }}</b></div>
                            <div class="match-phase-score">
                                <label><span>{{ $homeName }}</span><input type="number" name="{{ $phase['home'] }}" min="0" max="99" value="{{ old($phase['home'], $match->{$phase['home']}) }}"></label>
                                <em>:</em>
                                <label><span>{{ $awayName }}</span><input type="number" name="{{ $phase['away'] }}" min="0" max="99" value="{{ old($phase['away'], $match->{$phase['away']}) }}"></label>
                            </div>
                        </div>
                    @endforeach
                </div></div>
            </section>

            <section class="match-control-card">
                <header><span class="match-card-icon"><i class="material-icons">campaign</i></span><div><h4>{{ __('backend.matchPublishing') }}</h4><small>{{ __('backend.status') }}</small></div></header>
                <div class="match-card-body match-publishing-options">
                    <label class="match-toggle-option" for="is_home"><div><i class="material-icons">home</i><span><b>{{ __('backend.showMatchOnHome') }}</b><small>{{ __('backend.is_home') }}</small></span></div><input type="hidden" name="is_home" value="0"><input type="checkbox" id="is_home" name="is_home" value="1" @checked(old('is_home', $match->is_home))><span class="match-switch"></span></label>
                    <label class="match-toggle-option" for="is_slider"><div><i class="material-icons">view_carousel</i><span><b>{{ __('backend.showMatchInSlider') }}</b><small>{{ __('backend.is_slider') }}</small></span></div><input type="hidden" name="is_slider" value="0"><input type="checkbox" id="is_slider" name="is_slider" value="1" @checked(old('is_slider', $match->is_slider))><span class="match-switch"></span></label>
                </div>
            </section>

            <div class="match-form-actions"><button type="submit" class="btn btn-lg btn-primary"><i class="material-icons">save</i> {{ __('backend.saveMatchChanges') }}</button><a href="{{ route('leaguesRounds', ['league_id' => $match->league_id, 'season_id' => $match->season_id]) }}" class="btn btn-lg btn-default">{{ __('backend.cancel') }}</a></div>
        </form>

        <section class="match-control-card match-goals-card" id="match-goals">
            <header>
                <span class="match-card-icon match-goal-icon"><i class="material-icons">sports_soccer</i></span>
                <div><h4>{{ __('backend.matchGoalsEvents') }}</h4><small>{{ __('backend.matchGoalsEventsHint') }}</small></div>
                <span class="match-goals-count">{{ $goalEvents->count() }}</span>
            </header>
            <div class="match-card-body">
                <form method="POST" action="{{ route('matchGoalsStore', ['fixture' => $match->id]) }}" class="goal-event-form goal-event-create">
                    @csrf
                    <div class="goal-field">
                        <label for="goal_team_id">{{ __('backend.matchGoalTeam') }}</label>
                        <select name="team_id" id="goal_team_id" class="form-control goal-team-select" required>
                            <option value="">{{ __('backend.select') }}</option>
                            <option value="{{ $match->home_team_id }}" @selected(old('team_id') == $match->home_team_id)>{{ $homeName }}</option>
                            <option value="{{ $match->away_team_id }}" @selected(old('team_id') == $match->away_team_id)>{{ $awayName }}</option>
                        </select>
                    </div>
                    <div class="goal-field goal-player-field">
                        <label for="goal_player_id">{{ __('backend.matchGoalScorer') }}</label>
                        <select name="player_id" id="goal_player_id" class="form-control goal-player-select select2" required>
                            <option value="">{{ __('backend.matchGoalSelectPlayer') }}</option>
                            @foreach($homePlayers as $player)
                                <option value="{{ $player->id }}" data-team="{{ $match->home_team_id }}" @selected(old('player_id') == $player->id)>{{ $playerName($player) }}</option>
                            @endforeach
                            @foreach($awayPlayers as $player)
                                <option value="{{ $player->id }}" data-team="{{ $match->away_team_id }}" @selected(old('player_id') == $player->id)>{{ $playerName($player) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="goal-field goal-minute-field">
                        <label for="goal_minute">{{ __('backend.matchGoalMinute') }}</label>
                        <input type="number" name="minute" id="goal_minute" class="form-control" min="0" max="180" value="{{ old('minute') }}" required>
                    </div>
                    <div class="goal-field goal-minute-field">
                        <label for="goal_extra_minute">{{ __('backend.matchGoalExtraMinute') }}</label>
                        <input type="number" name="extra_minute" id="goal_extra_minute" class="form-control" min="0" max="60" value="{{ old('extra_minute') }}" placeholder="0">
                    </div>
                    <button type="submit" class="btn btn-primary goal-add-button"><i class="material-icons">add_circle</i>{{ __('backend.matchGoalAdd') }}</button>
                </form>

                <div class="goal-events-timeline">
                    @forelse($goalEvents as $goalEvent)
                        <article class="goal-event-row">
                            <div class="goal-event-time">
                                <i class="material-icons">sports_soccer</i>
                                <strong>{{ $goalEvent->minute }}@if($goalEvent->extra_minute)+{{ $goalEvent->extra_minute }}@endif′</strong>
                                <small>{{ $goalEvent->result }}</small>
                            </div>
                            <form method="POST" action="{{ route('matchGoalsUpdate', ['fixture' => $match->id, 'event' => $goalEvent->id]) }}" class="goal-event-form goal-event-update">
                                @csrf
                                <div class="goal-field">
                                    <label>{{ __('backend.matchGoalTeam') }}</label>
                                    <select name="team_id" class="form-control goal-team-select" required>
                                        <option value="{{ $match->home_team_id }}" @selected($goalEvent->team_id == $match->home_team_id)>{{ $homeName }}</option>
                                        <option value="{{ $match->away_team_id }}" @selected($goalEvent->team_id == $match->away_team_id)>{{ $awayName }}</option>
                                    </select>
                                </div>
                                <div class="goal-field goal-player-field">
                                    <label>{{ __('backend.matchGoalScorer') }}</label>
                                    <select name="player_id" class="form-control goal-player-select select2" required>
                                        @foreach($homePlayers as $player)
                                            <option value="{{ $player->id }}" data-team="{{ $match->home_team_id }}" @selected($goalEvent->player_id == $player->id)>{{ $playerName($player) }}</option>
                                        @endforeach
                                        @foreach($awayPlayers as $player)
                                            <option value="{{ $player->id }}" data-team="{{ $match->away_team_id }}" @selected($goalEvent->player_id == $player->id)>{{ $playerName($player) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="goal-field goal-minute-field"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" value="{{ $goalEvent->minute }}" required></div>
                                <div class="goal-field goal-minute-field"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" value="{{ $goalEvent->extra_minute }}" placeholder="0"></div>
                                <button type="submit" class="btn btn-sm btn-primary goal-save-button" title="{{ __('backend.save') }}"><i class="material-icons">save</i></button>
                            </form>
                            <form method="POST" action="{{ route('matchGoalsDestroy', ['fixture' => $match->id, 'event' => $goalEvent->id]) }}" class="goal-delete-form" onsubmit="return confirm(@js(__('backend.matchGoalDeleteConfirm')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="{{ __('backend.delete') }}"><i class="material-icons">delete</i></button>
                            </form>
                        </article>
                    @empty
                        <div class="goal-events-empty"><i class="material-icons">sports_soccer</i><strong>{{ __('backend.matchGoalsEmpty') }}</strong><span>{{ __('backend.matchGoalsEmptyHint') }}</span></div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection

@push('after-styles')
<style>
.match-control-page{--mc-purple:#4d2c85;--mc-dark:#180d2b;--mc-yellow:#febb22}.match-page-heading{display:flex;align-items:center;justify-content:space-between;gap:20px}.match-page-heading h3{display:flex;align-items:center;gap:9px;margin-bottom:4px}.match-page-heading h3 i{color:var(--mc-yellow)}
.match-scoreboard{position:relative;overflow:hidden;margin:20px 0;color:#fff;border-radius:12px;background:linear-gradient(125deg,#10071e,#281344);box-shadow:0 18px 45px rgba(24,13,43,.2)}.match-scoreboard:after{content:"967";position:absolute;inset-inline-end:-25px;bottom:-55px;color:rgba(255,255,255,.035);font-size:190px;font-weight:900;font-style:italic;line-height:1;pointer-events:none}.match-scoreboard-meta{position:relative;z-index:1;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:15px;padding:13px 22px;border-bottom:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.62);font-size:11px}.match-scoreboard-meta strong{padding:7px 13px;color:var(--mc-dark);border-radius:20px;background:var(--mc-yellow);font-size:10px}.match-scoreboard-meta small{text-align:end}.match-scoreboard-body{position:relative;z-index:1;display:grid;grid-template-columns:1fr 210px 1fr;align-items:center;min-height:240px;padding:28px 7%}.match-scoreboard-team{display:grid;justify-items:center;gap:8px;min-width:0;text-align:center}.match-team-logo{display:grid;place-items:center;width:86px;height:86px;padding:8px;border-radius:50%;background:#fff;box-shadow:0 0 0 8px rgba(255,255,255,.07)}.match-team-logo img{width:100%;height:100%;object-fit:contain}.match-team-logo span{color:var(--mc-purple);font-size:30px;font-weight:900}.match-scoreboard-team b{max-width:100%;color:#fff;font-size:17px}.match-scoreboard-team small{color:rgba(255,255,255,.43);font-size:9px}.match-scoreboard-result{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;justify-items:center;gap:9px;direction:ltr}.match-scoreboard-result input{width:74px;height:82px;padding:0;border:1px solid rgba(255,255,255,.14);border-radius:10px;color:#fff;outline:0;background:rgba(255,255,255,.07);text-align:center;font-size:40px;font-weight:900}.match-scoreboard-result input:focus{border-color:var(--mc-yellow);box-shadow:0 0 0 3px rgba(254,187,34,.15)}.match-scoreboard-result em{color:var(--mc-yellow);font-size:34px;font-style:normal;font-weight:900}.match-scoreboard-result span{grid-column:1/-1;color:rgba(255,255,255,.45);font-size:10px;font-weight:900}
.match-control-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px}.match-control-card{overflow:hidden;margin-bottom:18px;border:1px solid #e0dce4;border-radius:10px;background:#fff;box-shadow:0 7px 22px rgba(24,13,43,.055)}.match-control-card>header{display:flex;align-items:center;gap:11px;padding:15px 18px;border-bottom:1px solid #ece8ef;background:#faf9fb}.match-card-icon{display:grid;place-items:center;width:38px;height:38px;border-radius:9px;color:var(--mc-purple);background:#eee8f5}.match-control-card header h4{margin:0 0 2px;color:var(--mc-dark);font-size:14px;font-weight:800}.match-control-card header small{color:#98909f;font-size:9px}.match-card-body{padding:18px}.match-card-body label{color:#5f5765;font-size:11px;font-weight:700}.match-card-body .form-control{min-height:43px;border-color:#ddd8e1}
.match-time-fields{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.match-time-fields label{margin:0}.match-time-fields label>span{display:block;min-height:33px;margin-bottom:7px}.match-time-fields label>div{position:relative}.match-time-fields input{width:100%;height:50px;padding:0 30px 0 10px;border:1px solid #ddd8e1;border-radius:7px;color:var(--mc-dark);background:#faf9fb;text-align:center;font-size:19px;font-weight:900}.match-time-fields small{position:absolute;top:50%;inset-inline-end:11px;color:var(--mc-purple);transform:translateY(-50%);font-size:14px}
.match-phase-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:13px}.match-phase-card{overflow:hidden;border:1px solid #e1dce5;border-radius:9px;background:#faf9fb}.match-phase-title{display:flex;align-items:center;gap:9px;padding:10px 12px;border-bottom:1px solid #e7e2ea}.match-phase-title span{padding:5px 7px;color:var(--mc-dark);border-radius:4px;background:var(--mc-yellow);font-size:8px;font-weight:900}.match-phase-title b{color:#574e5e;font-size:10px}.match-phase-score{display:grid;grid-template-columns:1fr auto 1fr;align-items:end;gap:7px;padding:13px 10px;direction:ltr}.match-phase-score label{display:grid;gap:6px;margin:0;text-align:center}.match-phase-score label span{overflow:hidden;color:#918997;font-size:8px;white-space:nowrap;text-overflow:ellipsis}.match-phase-score input{width:100%;height:48px;padding:0;border:1px solid #dad4de;border-radius:7px;color:var(--mc-dark);background:#fff;text-align:center;font-size:22px;font-weight:900}.match-phase-score em{padding-bottom:13px;color:var(--mc-yellow);font-size:19px;font-style:normal;font-weight:900}
.match-publishing-options{display:grid;grid-template-columns:1fr 1fr;gap:14px}.match-toggle-option{display:flex;align-items:center;justify-content:space-between;gap:15px;margin:0;padding:15px;border:1px solid #e1dce5;border-radius:9px;cursor:pointer}.match-toggle-option>div{display:flex;align-items:center;gap:10px}.match-toggle-option>div>i{color:var(--mc-purple)}.match-toggle-option b,.match-toggle-option small{display:block}.match-toggle-option b{color:var(--mc-dark);font-size:11px}.match-toggle-option small{margin-top:3px;color:#9a929f;font-size:8px}.match-toggle-option input[type=checkbox]{position:absolute;opacity:0}.match-switch{position:relative;flex:none;width:45px;height:24px;border-radius:20px;background:#d8d2dc;transition:.25s}.match-switch:after{content:"";position:absolute;top:3px;inset-inline-start:3px;width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 2px 5px rgba(0,0,0,.2);transition:.25s}.match-toggle-option input:checked+.match-switch{background:var(--mc-purple)}.match-toggle-option input:checked+.match-switch:after{transform:translateX(21px)}[dir=rtl] .match-toggle-option input:checked+.match-switch:after{transform:translateX(-21px)}.match-form-actions{display:flex;align-items:center;gap:10px;padding:5px 0 25px}.match-form-actions .btn{display:inline-flex;align-items:center;gap:7px}
.match-goals-card>header{position:relative}.match-goal-icon{color:var(--mc-dark);background:var(--mc-yellow)}.match-goals-count{display:grid;place-items:center;margin-inline-start:auto;width:34px;height:34px;border-radius:50%;color:#fff;background:var(--mc-purple);font-size:12px;font-weight:900}.goal-event-form{display:grid;align-items:end;gap:12px}.goal-event-create{grid-template-columns:1fr minmax(220px,1.6fr) 100px 110px auto;padding:15px;border:1px solid #e3dce9;border-radius:10px;background:#faf8fc}.goal-field{min-width:0}.goal-field label{display:block;margin-bottom:7px}.goal-field .form-control{width:100%;height:43px}.goal-add-button{display:inline-flex;align-items:center;justify-content:center;gap:6px;height:43px;white-space:nowrap}.goal-add-button i{font-size:18px}.goal-events-timeline{display:grid;gap:10px;margin-top:18px}.goal-event-row{display:grid;grid-template-columns:96px minmax(0,1fr) auto;align-items:center;gap:12px;padding:11px;border:1px solid #e5e0e8;border-radius:10px;background:#fff}.goal-event-time{display:grid;grid-template-columns:auto 1fr;align-items:center;gap:2px 7px;padding:10px;border-radius:8px;color:#fff;background:var(--mc-dark)}.goal-event-time i{grid-row:1/3;color:var(--mc-yellow);font-size:22px}.goal-event-time strong{font-size:14px}.goal-event-time small{color:rgba(255,255,255,.55);font-size:9px}.goal-event-update{grid-template-columns:1fr minmax(190px,1.5fr) 82px 90px 42px}.goal-save-button,.goal-delete-form .btn{display:grid;place-items:center;width:40px;height:40px;padding:0}.goal-save-button i,.goal-delete-form i{font-size:18px}.goal-events-empty{display:grid;justify-items:center;gap:5px;padding:30px;color:#928a98;border:1px dashed #d9d1df;border-radius:10px;background:#fcfbfd;text-align:center}.goal-events-empty i{color:#c4b9ce;font-size:34px}.goal-events-empty strong{color:#5b5260;font-size:12px}.goal-events-empty span{font-size:9px}
@media(max-width:991px){.match-scoreboard-body{grid-template-columns:1fr 160px 1fr;padding-inline:4%}.match-phase-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:1100px){.goal-event-create{grid-template-columns:1fr 1.5fr 90px 100px}.goal-add-button{grid-column:1/-1}.goal-event-row{grid-template-columns:86px minmax(0,1fr) auto}.goal-event-update{grid-template-columns:1fr 1.5fr 75px 85px}.goal-save-button{grid-column:1/-1;width:100%}}
@media(max-width:767px){.match-page-heading{align-items:flex-start}.match-scoreboard-meta{grid-template-columns:1fr auto}.match-scoreboard-meta small{display:none}.match-scoreboard-body{grid-template-columns:1fr 110px 1fr;min-height:205px;padding:22px 10px}.match-team-logo{width:62px;height:62px}.match-scoreboard-team b{font-size:11px}.match-scoreboard-result input{width:44px;height:60px;font-size:28px}.match-control-grid,.match-publishing-options{grid-template-columns:1fr}.match-time-fields{grid-template-columns:1fr}.match-time-fields label>span{min-height:0}.match-phase-grid{grid-template-columns:1fr}.match-form-actions{align-items:stretch;flex-direction:column}.match-form-actions .btn{justify-content:center}.goal-event-create,.goal-event-update{grid-template-columns:1fr 1fr}.goal-player-field{grid-column:1/-1}.goal-event-row{grid-template-columns:1fr auto}.goal-event-time{grid-column:1/-1}.goal-event-update{grid-column:1/-1}.goal-save-button{grid-column:1/-1}.goal-delete-form{align-self:end}}
</style>
@endpush

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
    var status=document.getElementById('state_code'),minute=document.getElementById('match_minute'),statusPreview=document.getElementById('match-status-preview'),minutePreview=document.getElementById('scoreboard-minute');
    function refreshPreview(){if(status&&statusPreview)statusPreview.textContent=status.options[status.selectedIndex].text;if(minutePreview)minutePreview.textContent=minute&&minute.value!==''?minute.value+'′':'VS'}
    if(status)status.addEventListener('change',refreshPreview);if(minute)minute.addEventListener('input',refreshPreview);
    function filterPlayers(teamSelect){
        var form=teamSelect.closest('.goal-event-form'),playerSelect=form?form.querySelector('.goal-player-select'):null,team=teamSelect.value;
        if(!playerSelect)return;
        Array.prototype.forEach.call(playerSelect.options,function(option){if(!option.value)return;option.disabled=!!team&&option.getAttribute('data-team')!==team;});
        if(playerSelect.selectedOptions.length&&playerSelect.selectedOptions[0].disabled)playerSelect.value='';
        if(window.jQuery&&jQuery.fn.select2&&jQuery(playerSelect).hasClass('select2-hidden-accessible'))jQuery(playerSelect).trigger('change.select2');
    }
    document.querySelectorAll('.goal-team-select').forEach(function(select){filterPlayers(select);select.addEventListener('change',function(){filterPlayers(select);});});
});
</script>
@endpush
