<section class="match-control-card match-results-card">
    <header>
        <span class="match-card-icon"><i class="material-icons">scoreboard</i></span>
        <div><h4>{{ __('backend.scoreManagement') }}</h4><small>{{ __('backend.scoreAndGoalsHint') }}</small></div>
        <button type="submit" form="match-main-form" class="btn btn-primary match-inline-save"><i class="material-icons">save</i>{{ __('backend.saveScores') }}</button>
    </header>
    <div class="match-card-body"><div class="match-phase-grid">
        @foreach($scorePhases as $phase)
            <div class="match-phase-card">
                <div class="match-phase-title"><span>{{ $phase['code'] }}</span><b>{{ $phase['label'] }}</b></div>
                <div class="match-phase-score">
                    <label><span>{{ $homeName }}</span><input form="match-main-form" type="number" name="{{ $phase['home'] }}" min="0" max="99" value="{{ old($phase['home'], $match->{$phase['home']}) }}"></label>
                    <em>:</em>
                    <label><span>{{ $awayName }}</span><input form="match-main-form" type="number" name="{{ $phase['away'] }}" min="0" max="99" value="{{ old($phase['away'], $match->{$phase['away']}) }}"></label>
                </div>
            </div>
        @endforeach
    </div></div>
</section>

<section class="match-control-card match-events-card" id="match-goals">
    <header>
        <span class="match-card-icon event-icon-goal"><i class="material-icons">sports_soccer</i></span>
        <div><h4>{{ __('backend.matchGoalsEvents') }}</h4><small>{{ __('backend.matchGoalsEventsHint') }}</small></div>
        <span class="match-events-count">{{ $goalEvents->count() }}</span>
    </header>
    <div class="match-card-body">
        <form method="POST" action="{{ route('matchGoalsStore', ['fixture' => $match->id]) }}" class="match-event-form event-create-form" data-event-form>
            @csrf
            <div class="event-field"><label>{{ __('backend.matchGoalTeam') }}</label><select name="team_id" class="form-control event-team-select" required><option value="">{{ __('backend.select') }}</option><option value="{{ $match->home_team_id }}">{{ $homeName }}</option><option value="{{ $match->away_team_id }}">{{ $awayName }}</option></select></div>
            <div class="event-field event-player-field"><label>{{ __('backend.matchGoalScorer') }}</label><select name="player_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => old('player_id')])</select></div>
            <div class="event-field event-minute"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" required></div>
            <div class="event-field event-minute"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" placeholder="0"></div>
            <button type="submit" class="btn btn-primary event-add-button"><i class="material-icons">add_circle</i>{{ __('backend.matchGoalAdd') }}</button>
        </form>

        <div class="match-event-list">
            @forelse($goalEvents as $event)
                <article class="match-event-row">
                    <div class="event-summary event-summary-goal"><i class="material-icons">sports_soccer</i><strong>{{ $event->minute }}@if($event->extra_minute)+{{ $event->extra_minute }}@endif′</strong><small>{{ $event->result }}</small></div>
                    <form method="POST" action="{{ route('matchGoalsUpdate', ['fixture' => $match->id, 'event' => $event->id]) }}" class="match-event-form event-update-form" data-event-form>
                        @csrf
                        <div class="event-field"><label>{{ __('backend.matchGoalTeam') }}</label><select name="team_id" class="form-control event-team-select" required><option value="{{ $match->home_team_id }}" @selected($event->team_id == $match->home_team_id)>{{ $homeName }}</option><option value="{{ $match->away_team_id }}" @selected($event->team_id == $match->away_team_id)>{{ $awayName }}</option></select></div>
                        <div class="event-field event-player-field"><label>{{ __('backend.matchGoalScorer') }}</label><select name="player_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => $event->player_id])</select></div>
                        <div class="event-field event-minute"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" value="{{ $event->minute }}" required></div>
                        <div class="event-field event-minute"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" value="{{ $event->extra_minute }}"></div>
                        <button type="submit" class="btn btn-primary event-save-button" title="{{ __('backend.save') }}"><i class="material-icons">save</i></button>
                    </form>
                    <form method="POST" action="{{ route('matchGoalsDestroy', ['fixture' => $match->id, 'event' => $event->id]) }}" onsubmit="return confirm(@js(__('backend.matchGoalDeleteConfirm')))" class="event-delete-form">@csrf @method('DELETE')<button class="btn btn-danger" title="{{ __('backend.delete') }}"><i class="material-icons">delete</i></button></form>
                </article>
            @empty
                <div class="match-events-empty"><i class="material-icons">sports_soccer</i><strong>{{ __('backend.matchGoalsEmpty') }}</strong><span>{{ __('backend.matchGoalsEmptyHint') }}</span></div>
            @endforelse
        </div>
    </div>
</section>
