@if($standingRows->isNotEmpty())
<div class="sm-table-wrap {{ !empty($compact) ? 'is-compact' : '' }}">
    <table class="sm-standings-table">
        <thead><tr><th>#</th><th>{{ __('matches.team') }}</th><th>{{ __('matches.playedShort') }}</th><th>{{ __('matches.wonShort') }}</th><th>{{ __('matches.drawShort') }}</th><th>{{ __('matches.lostShort') }}</th><th>{{ __('matches.goalDifferenceShort') }}</th><th>{{ __('matches.pointsShort') }}</th></tr></thead>
        <tbody>
        @foreach($standingRows as $row)
            @php
                $team = $row->participant;
                $teamName = app()->getLocale() === 'en' ? ($team?->name_en ?: $team?->name_ar) : ($team?->name_ar ?: $team?->name_en);
            @endphp
            <tr>
                <td><b>{{ $row->position ?: $loop->iteration }}</b></td>
                <td><span class="sm-table-team">@if($team?->image_path)<img src="{{ $team->image_path }}" alt="">@else<i>{{ mb_substr($teamName ?: __('matches.unknown'), 0, 1) }}</i>@endif<strong>{{ $teamName ?: __('matches.unknown') }}</strong></span></td>
                <td>{{ $row->played }}</td><td>{{ $row->won }}</td><td>{{ $row->draw }}</td><td>{{ $row->lost }}</td><td dir="ltr">{{ $row->goal_difference > 0 ? '+' : '' }}{{ $row->goal_difference }}</td><td><strong>{{ $row->points }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@else
    @include('frontEnd.matches.partials.empty', ['title' => __('matches.noStandings'), 'description' => __('matches.noStandingsDescription')])
@endif
