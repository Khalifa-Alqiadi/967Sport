@if ($WebmasterSection->sportmonks_status == 2 || $WebmasterSection->sportmonks_status == 3)
    <div class="form-group row">
        <label for="player_id" class="col-sm-2 form-control-label">
            {{ @Helper::currentLanguage()->code === 'ar' ? 'اللاعب' : 'Player' }}
        </label>
        <div class="col-sm-10">
            <select name="player_id" id="player_id" class="form-control select2"
                ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                <option value="">....</option>
                @if($type === 'edit' && !empty($players))
                    @foreach($players as $player)
                        <option value="{{ $player->id }}" {{ (int) $Topic->player_id === (int) $player->id ? 'selected' : '' }}>
                            {{ $player->$name_var ?: $player->name_en ?: $player->name_ar }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
@else
    <input type="hidden" name="player_id" id="player_id" value="">
@endif
