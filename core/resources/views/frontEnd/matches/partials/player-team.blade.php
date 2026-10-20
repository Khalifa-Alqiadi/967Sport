@php
    $affiliationName = app()->getLocale() === 'en'
        ? ($affiliation->name_en ?: $affiliation->name_ar)
        : ($affiliation->name_ar ?: $affiliation->name_en);
    $affiliationUrl = app()->getLocale() === 'en'
        ? route('sport.localized.team', ['lang' => 'en', 'team' => $affiliation->id])
        : route('sport.team', $affiliation->id);
@endphp
<a class="fp-club" href="{{ $affiliationUrl }}">
    <span class="fp-club__mark">@if($affiliation->image_path)<img src="{{ $affiliation->image_path }}" alt="" loading="lazy">@else<i class="bi bi-shield" aria-hidden="true"></i>@endif</span>
    <span class="fp-club__name"><strong>{{ $affiliationName ?: __('footballProfiles.unknown') }}</strong>
        <small>
            @if($affiliation->pivot?->from_date){{ __('footballProfiles.from') }} {{ \Carbon\Carbon::parse($affiliation->pivot->from_date)->format('Y') }}@endif
            @if($affiliation->pivot?->to_date) · {{ __('footballProfiles.to') }} {{ \Carbon\Carbon::parse($affiliation->pivot->to_date)->format('Y') }}@elseif($affiliation->pivot?->is_current) · {{ __('footballProfiles.ongoing') }}@endif
        </small>
    </span>
    @if($affiliation->pivot?->jersey_number)<b class="fp-club__number">#{{ $affiliation->pivot->jersey_number }}</b>@endif
</a>
