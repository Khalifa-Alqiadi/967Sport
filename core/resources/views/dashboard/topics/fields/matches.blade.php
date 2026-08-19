@if ($WebmasterSection->sportmonks_status == 3)
    <div class="form-group row">
        <label for="match_id" class="col-sm-2 form-control-label">{!! __('backend.matches') !!}</label>
        <div class="col-sm-10">
            @if($type == 'edit')
                <select name="match_id" id="match_id" class="form-control select2"
                    ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                    <option value="">....</option>
                    @foreach ($matches as $match)
                        <option value="{{ $match->id }}" {{ $Topic->fixture_id == $match->id ? 'selected' : '' }}>
                            {!! $match?->homeTeam?->$name_var !!} vs {!! $match?->awayTeam?->$name_var !!}
                            @if($match->starting_at)
                                ({{ \Carbon\Carbon::parse($match->starting_at)->format('d/m/Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            @else
                <select name="match_id" id="match_id" class="form-control select2"
                    ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                    <option value="">....</option>
                </select>
            @endif
        </div>
    </div>
@else
    <input type="hidden" name="match_id" id="match_id" value="0">
@endif
