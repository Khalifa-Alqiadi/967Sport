<option value="">{{ __('backend.select') }}</option>
@foreach($homePlayers as $player)
    <option value="{{ $player->id }}" data-team="{{ $match->home_team_id }}" @selected((string) ($selectedPlayer ?? '') === (string) $player->id)>{{ $playerName($player) }}</option>
@endforeach
@foreach($awayPlayers as $player)
    <option value="{{ $player->id }}" data-team="{{ $match->away_team_id }}" @selected((string) ($selectedPlayer ?? '') === (string) $player->id)>{{ $playerName($player) }}</option>
@endforeach
