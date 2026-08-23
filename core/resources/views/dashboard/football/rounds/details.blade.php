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
                        <input type="number" name="home_score" min="0" max="99" value="{{ old('home_score', $match->home_score) }}" aria-label="{{ __('backend.homeGoals') }}">
                        <em>:</em>
                        <input type="number" name="away_score" min="0" max="99" value="{{ old('away_score', $match->away_score) }}" aria-label="{{ __('backend.awayGoals') }}">
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
@media(max-width:991px){.match-scoreboard-body{grid-template-columns:1fr 160px 1fr;padding-inline:4%}.match-phase-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:767px){.match-page-heading{align-items:flex-start}.match-scoreboard-meta{grid-template-columns:1fr auto}.match-scoreboard-meta small{display:none}.match-scoreboard-body{grid-template-columns:1fr 110px 1fr;min-height:205px;padding:22px 10px}.match-team-logo{width:62px;height:62px}.match-scoreboard-team b{font-size:11px}.match-scoreboard-result input{width:44px;height:60px;font-size:28px}.match-control-grid,.match-publishing-options{grid-template-columns:1fr}.match-time-fields{grid-template-columns:1fr}.match-time-fields label>span{min-height:0}.match-phase-grid{grid-template-columns:1fr}.match-form-actions{align-items:stretch;flex-direction:column}.match-form-actions .btn{justify-content:center}}
</style>
@endpush

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){var status=document.getElementById('state_code'),minute=document.getElementById('match_minute'),statusPreview=document.getElementById('match-status-preview'),minutePreview=document.getElementById('scoreboard-minute');function refreshPreview(){if(status&&statusPreview)statusPreview.textContent=status.options[status.selectedIndex].text;if(minutePreview)minutePreview.textContent=minute&&minute.value!==''?minute.value+'′':'VS'}if(status)status.addEventListener('change',refreshPreview);if(minute)minute.addEventListener('input',refreshPreview)});
</script>
@endpush
