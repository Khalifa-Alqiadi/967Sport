<ul class="nav nav-md light dk">
    <li class="nav-item inline">
        <a class="nav-link {{ $tab == 'details' ? 'active' : '' }}" href="{{ route('teams.edit', ['id' => $team->id, 'tab' => 'details']) }}">
            <span class="text-md"><i class="material-icons">&#xe8ed;</i> {{ __('backend.details') }}</span>
        </a>
    </li>
    <li class="nav-item inline">
        <a class="nav-link {{ $tab == 'players' ? 'active' : '' }}" href="{{ route('teams.edit', ['id' => $team->id, 'tab' => 'players']) }}">
            <span class="text-md"><i class="material-icons">&#xe7fb;</i> {{ __('backend.players') }}</span>
        </a>
    </li>
</ul>
