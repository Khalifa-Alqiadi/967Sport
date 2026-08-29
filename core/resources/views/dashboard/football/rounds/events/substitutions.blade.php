<section class="match-control-card match-events-card" id="match-substitutions">
    <header><span class="match-card-icon event-icon-sub"><i class="material-icons">swap_horiz</i></span><div><h4>{{ __('backend.matchSubstitutions') }}</h4><small>{{ __('backend.matchSubstitutionsHint') }}</small></div><span class="match-events-count">{{ $substitutionEvents->count() }}</span></header>
    <div class="match-card-body">
        <form method="POST" action="{{ route('matchSubstitutionsStore', ['fixture' => $match->id]) }}" class="match-event-form substitution-create-form" data-event-form>
            @csrf
            <div class="event-field"><label>{{ __('backend.matchEventTeam') }}</label><select name="team_id" class="form-control event-team-select" required><option value="">{{ __('backend.select') }}</option><option value="{{ $match->home_team_id }}">{{ $homeName }}</option><option value="{{ $match->away_team_id }}">{{ $awayName }}</option></select></div>
            <div class="event-field event-player-field"><label>{{ __('backend.playerOut') }}</label><select name="player_out_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => old('player_out_id')])</select></div>
            <div class="event-field event-player-field"><label>{{ __('backend.playerIn') }}</label><select name="player_in_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => old('player_in_id')])</select></div>
            <div class="event-field event-minute"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" required></div>
            <div class="event-field event-minute"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" placeholder="0"></div>
            <button type="submit" class="btn btn-primary event-add-button"><i class="material-icons">add_circle</i>{{ __('backend.matchSubstitutionAdd') }}</button>
        </form>

        <div class="match-event-list">
            @forelse($substitutionEvents as $event)
                <article class="match-event-row">
                    <div class="event-summary event-summary-sub"><i class="material-icons">swap_horiz</i><strong>{{ $event->minute }}@if($event->extra_minute)+{{ $event->extra_minute }}@endif′</strong><small>{{ __('backend.substitution') }}</small></div>
                    <form method="POST" action="{{ route('matchSubstitutionsUpdate', ['fixture' => $match->id, 'event' => $event->id]) }}" class="match-event-form substitution-update-form" data-event-form>
                        @csrf
                        <div class="event-field"><label>{{ __('backend.matchEventTeam') }}</label><select name="team_id" class="form-control event-team-select" required><option value="{{ $match->home_team_id }}" @selected($event->team_id == $match->home_team_id)>{{ $homeName }}</option><option value="{{ $match->away_team_id }}" @selected($event->team_id == $match->away_team_id)>{{ $awayName }}</option></select></div>
                        <div class="event-field event-player-field"><label>{{ __('backend.playerOut') }}</label><select name="player_out_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => $event->player_out_id])</select></div>
                        <div class="event-field event-player-field"><label>{{ __('backend.playerIn') }}</label><select name="player_in_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => $event->player_in_id])</select></div>
                        <div class="event-field event-minute"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" value="{{ $event->minute }}" required></div>
                        <div class="event-field event-minute"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" value="{{ $event->extra_minute }}"></div>
                        <button type="submit" class="btn btn-primary event-save-button"><i class="material-icons">save</i></button>
                    </form>
                    <form method="POST" action="{{ route('matchSubstitutionsDestroy', ['fixture' => $match->id, 'event' => $event->id]) }}" onsubmit="return confirm(@js(__('backend.matchSubstitutionDeleteConfirm')))" class="event-delete-form">@csrf @method('DELETE')<button class="btn btn-danger"><i class="material-icons">delete</i></button></form>
                </article>
            @empty
                <div class="match-events-empty"><i class="material-icons">swap_horiz</i><strong>{{ __('backend.matchSubstitutionsEmpty') }}</strong><span>{{ __('backend.matchSubstitutionsEmptyHint') }}</span></div>
            @endforelse
        </div>
    </div>
</section>
