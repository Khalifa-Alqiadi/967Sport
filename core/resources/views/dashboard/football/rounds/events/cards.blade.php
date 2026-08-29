<section class="match-control-card match-events-card" id="match-cards">
    <header><span class="match-card-icon event-icon-card"><i class="material-icons">style</i></span><div><h4>{{ __('backend.matchCards') }}</h4><small>{{ __('backend.matchCardsHint') }}</small></div><span class="match-events-count">{{ $cardEvents->count() }}</span></header>
    <div class="match-card-body">
        <form method="POST" action="{{ route('matchCardsStore', ['fixture' => $match->id]) }}" class="match-event-form card-create-form" data-event-form>
            @csrf
            <div class="event-field"><label>{{ __('backend.matchEventTeam') }}</label><select name="team_id" class="form-control event-team-select" required><option value="">{{ __('backend.select') }}</option><option value="{{ $match->home_team_id }}">{{ $homeName }}</option><option value="{{ $match->away_team_id }}">{{ $awayName }}</option></select></div>
            <div class="event-field event-player-field"><label>{{ __('backend.matchCardPlayer') }}</label><select name="player_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => old('player_id')])</select></div>
            <div class="event-field"><label>{{ __('backend.matchCardType') }}</label><select name="card_type" class="form-control" required><option value="yellow_card">{{ __('backend.yellowCard') }}</option><option value="red_card">{{ __('backend.redCard') }}</option></select></div>
            <div class="event-field event-minute"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" required></div>
            <div class="event-field event-minute"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" placeholder="0"></div>
            <button type="submit" class="btn btn-primary event-add-button"><i class="material-icons">add_circle</i>{{ __('backend.matchCardAdd') }}</button>
        </form>

        <div class="match-event-list">
            @forelse($cardEvents as $event)
                <article class="match-event-row">
                    <div class="event-summary {{ $event->type === 'red_card' ? 'event-summary-red' : 'event-summary-yellow' }}"><i class="material-icons">style</i><strong>{{ $event->minute }}@if($event->extra_minute)+{{ $event->extra_minute }}@endif′</strong><small>{{ $event->type === 'red_card' ? __('backend.redCard') : __('backend.yellowCard') }}</small></div>
                    <form method="POST" action="{{ route('matchCardsUpdate', ['fixture' => $match->id, 'event' => $event->id]) }}" class="match-event-form card-update-form" data-event-form>
                        @csrf
                        <div class="event-field"><label>{{ __('backend.matchEventTeam') }}</label><select name="team_id" class="form-control event-team-select" required><option value="{{ $match->home_team_id }}" @selected($event->team_id == $match->home_team_id)>{{ $homeName }}</option><option value="{{ $match->away_team_id }}" @selected($event->team_id == $match->away_team_id)>{{ $awayName }}</option></select></div>
                        <div class="event-field event-player-field"><label>{{ __('backend.matchCardPlayer') }}</label><select name="player_id" class="form-control event-player-select select2" required>@include('dashboard.football.rounds.events.player-options', ['selectedPlayer' => $event->player_id])</select></div>
                        <div class="event-field"><label>{{ __('backend.matchCardType') }}</label><select name="card_type" class="form-control"><option value="yellow_card" @selected($event->type === 'yellow_card')>{{ __('backend.yellowCard') }}</option><option value="red_card" @selected($event->type === 'red_card')>{{ __('backend.redCard') }}</option></select></div>
                        <div class="event-field event-minute"><label>{{ __('backend.matchGoalMinute') }}</label><input type="number" name="minute" class="form-control" min="0" max="180" value="{{ $event->minute }}" required></div>
                        <div class="event-field event-minute"><label>{{ __('backend.matchGoalExtraMinute') }}</label><input type="number" name="extra_minute" class="form-control" min="0" max="60" value="{{ $event->extra_minute }}"></div>
                        <button type="submit" class="btn btn-primary event-save-button"><i class="material-icons">save</i></button>
                    </form>
                    <form method="POST" action="{{ route('matchCardsDestroy', ['fixture' => $match->id, 'event' => $event->id]) }}" onsubmit="return confirm(@js(__('backend.matchCardDeleteConfirm')))" class="event-delete-form">@csrf @method('DELETE')<button class="btn btn-danger"><i class="material-icons">delete</i></button></form>
                </article>
            @empty
                <div class="match-events-empty"><i class="material-icons">style</i><strong>{{ __('backend.matchCardsEmpty') }}</strong><span>{{ __('backend.matchCardsEmptyHint') }}</span></div>
            @endforelse
        </div>
    </div>
</section>
