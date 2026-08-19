@php
    $pickedFixture = $selectedFixture ?? null;
    $pickedLeagueId = old('league_id', $pickedFixture?->league_id);
    $pickedSeasonId = old('season_id', $pickedFixture?->season_id);
    $pickedFixtureId = old('fixture_id', $pickedFixture?->id);
    $languageCode = Helper::currentLanguage()?->code === 'en' ? 'en' : 'ar';
@endphp

<div class="form-group row">
    <label class="col-sm-2 form-control-label">{{ __('backend.bannerMatch') }}</label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-md-4 m-b-sm">
                <label for="banner_league_id" class="text-muted small">{{ __('backend.bannerLeague') }}</label>
                <select name="league_id" id="banner_league_id" class="form-control select2">
                    <option value="">{{ __('backend.bannerSelectLeague') }}</option>
                    @foreach($Leagues as $League)
                        @php($leagueName = $languageCode === 'en' ? ($League->name_en ?: $League->name_ar) : ($League->name_ar ?: $League->name_en))
                        <option value="{{ $League->id }}" @selected((string) $pickedLeagueId === (string) $League->id)>{{ $leagueName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 m-b-sm">
                <label for="banner_season_id" class="text-muted small">{{ __('backend.bannerSeason') }}</label>
                <select name="season_id" id="banner_season_id" class="form-control select2" disabled>
                    <option value="">{{ __('backend.bannerSelectSeason') }}</option>
                </select>
            </div>
            <div class="col-md-4 m-b-sm">
                <label for="fixture_id" class="text-muted small">{{ __('backend.bannerFixture') }}</label>
                <select name="fixture_id" id="fixture_id" class="form-control select2" disabled>
                    <option value="">{{ __('backend.bannerSelectFixture') }}</option>
                </select>
            </div>
        </div>
        <small class="text-muted"><i class="material-icons" style="font-size: 15px; vertical-align: middle">info_outline</i> {{ __('backend.bannerMatchHint') }}</small>
    </div>
</div>

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var league = document.getElementById('banner_league_id');
    var season = document.getElementById('banner_season_id');
    var fixture = document.getElementById('fixture_id');
    if (!league || !season || !fixture) return;

    var selectedSeason = @json((string) $pickedSeasonId);
    var selectedFixture = @json((string) $pickedFixtureId);
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
    var texts = {
        season: @json(__('backend.bannerSelectSeason')),
        fixture: @json(__('backend.bannerSelectFixture')),
        loading: @json(__('backend.bannerLoading')),
        noSeasons: @json(__('backend.bannerNoSeasons')),
        noMatches: @json(__('backend.bannerNoMatches')),
        currentSeason: @json(__('backend.bannerCurrentSeason'))
    };

    function resetSelect(select, label, disabled) {
        select.innerHTML = '<option value="">' + label + '</option>';
        select.disabled = disabled;
        if (window.jQuery && jQuery.fn.select2) jQuery(select).trigger('change.select2');
    }

    function post(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
            body: JSON.stringify(data)
        }).then(function (response) {
            if (!response.ok) throw new Error('Request failed');
            return response.json();
        });
    }

    function loadMatches() {
        resetSelect(fixture, texts.loading, true);
        if (!league.value || !season.value) {
            resetSelect(fixture, texts.fixture, true);
            return;
        }
        post(@json(route('banners.league.matches')), {league_id: league.value, season_id: season.value})
            .then(function (response) {
                resetSelect(fixture, response.matches.length ? texts.fixture : texts.noMatches, false);
                response.matches.forEach(function (item) {
                    fixture.add(new Option(item.name, item.id, false, String(item.id) === selectedFixture));
                });
                if (window.jQuery && jQuery.fn.select2) jQuery(fixture).trigger('change.select2');
                selectedFixture = '';
            }).catch(function () { resetSelect(fixture, texts.noMatches, true); });
    }

    function loadSeasons() {
        resetSelect(season, texts.loading, true);
        resetSelect(fixture, texts.fixture, true);
        if (!league.value) {
            resetSelect(season, texts.season, true);
            return;
        }
        post(@json(route('banners.league.seasons')), {league_id: league.value})
            .then(function (response) {
                resetSelect(season, response.seasons.length ? texts.season : texts.noSeasons, false);
                response.seasons.forEach(function (item) {
                    var name = item.name + (item.is_current ? ' — ' + texts.currentSeason : '');
                    season.add(new Option(name, item.id, false, String(item.id) === selectedSeason));
                });
                if (window.jQuery && jQuery.fn.select2) jQuery(season).trigger('change.select2');
                if (selectedSeason) loadMatches();
                selectedSeason = '';
            }).catch(function () { resetSelect(season, texts.noSeasons, true); });
    }

    league.addEventListener('change', function () { selectedSeason = ''; selectedFixture = ''; loadSeasons(); });
    season.addEventListener('change', function () { selectedFixture = ''; loadMatches(); });
    if (league.value) loadSeasons();
});
</script>
@endpush
