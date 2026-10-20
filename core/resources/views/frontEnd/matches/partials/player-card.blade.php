@php
    $playerName = app()->getLocale() === 'en'
        ? ($squadPlayer->name_en ?: $squadPlayer->name_ar)
        : ($squadPlayer->name_ar ?: $squadPlayer->name_en);
    $playerLink = app()->getLocale() === 'en'
        ? route('sport.localized.player', ['lang' => 'en', 'player' => $squadPlayer->id])
        : route('sport.player', $squadPlayer->id);
@endphp
<a class="fp-player-card" href="{{ $playerLink }}">
    <span class="fp-player-card__photo">
        @if($squadPlayer->image_path)<img src="{{ $squadPlayer->image_path }}" alt="{{ $playerName }}" loading="lazy">
        @else<i class="bi bi-person-fill" aria-hidden="true"></i>@endif
    </span>
    <span class="fp-player-card__body">
        <strong>{{ $playerName ?: __('footballProfiles.unknown') }}</strong>
        <small>@if($squadPlayer->pivot?->jersey_number)#{{ $squadPlayer->pivot->jersey_number }}@else{{ __('footballProfiles.playerProfile') }}@endif</small>
    </span>
    <i class="bi bi-arrow-up-left" aria-hidden="true"></i>
</a>
