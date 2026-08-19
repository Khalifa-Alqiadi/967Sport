@if ($WebmasterSection->sportmonks_status == 2 || $WebmasterSection->sportmonks_status == 3)
    <div class="form-group row">
        <label for="season_id" class="col-sm-2 form-control-label">{!! __('backend.season') !!}</label>
        <div class="col-sm-10">
            @if($type == 'edit')
                <select name="season_id" id="season_id" class="form-control select2"
                    ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                    <option value="">....</option>
                    @if(!empty($seasons))
                        @foreach ($seasons as $season)
                            @php $sid = $season['id'] ?? $season->id; $sname = $season['name'] ?? $season->name; $sCurrent = $season['is_current'] ?? $season->is_current ?? false; @endphp
                            <option value="{{ $sid }}" {{ $Topic->season_id == $sid ? 'selected' : '' }}>
                                {{ $sname }}{{ $sCurrent ? ' (' . __('backend.current') . ')' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            @else
                <select name="season_id" id="season_id" class="form-control select2"
                    ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                    <option value="">....</option>
                </select>
            @endif
        </div>
    </div>
@else
    <input type="hidden" name="season_id" id="season_id" value="">
@endif
