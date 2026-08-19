@push('after-scripts')
    @if ($WebmasterSection->sportmonks_status == 2 || $WebmasterSection->sportmonks_status == 3)
    <script>
        $(document).ready(function() {

            // League changes → load seasons
            $('#league_id').on('change', function() {
                let leagueId = $(this).val();

                $('#season_id').html('<option value="">جاري التحميل...</option>').trigger('change');
                $('#team_id').html('<option value="">....</option>').trigger('change');
                $('#player_id').html('<option value="">....</option>').trigger('change');
                $('#match_id').html('<option value="">....</option>').trigger('change');

                if (!leagueId) {
                    $('#season_id').html('<option value="">....</option>').trigger('change');
                    return;
                }
 
                $.ajax({
                    type: "POST",
                    url: "{{ route('topics.league.seasons') }}",
                    data: { _token: "{{ csrf_token() }}", league_id: leagueId },
                    success: function(data) {
                        let options = '<option value="">....</option>';
                        if (data.ok && data.seasons.length > 0) {
                            $.each(data.seasons, function(i, s) {
                                let label = s.name + (s.is_current ? ' ({{ __("backend.current") }})' : '');
                                options += `<option value="${s.id}">${label}</option>`;
                            });
                        }
                        $('#season_id').html(options).trigger('change');
                    },
                    error: function() {
                        $('#season_id').html('<option value="">فشل تحميل المواسم</option>').trigger('change');
                    }
                });
            });

            // Season changes → load teams filtered by season
            $('#season_id').on('change', function() {
                let leagueId = $('#league_id').val();
                let seasonId = $(this).val();

                $('#team_id').html('<option value="">جاري التحميل...</option>').trigger('change');
                $('#player_id').html('<option value="">....</option>').trigger('change');
                $('#match_id').html('<option value="">....</option>').trigger('change');

                if (!seasonId) {
                    $('#team_id').html('<option value="">....</option>').trigger('change');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('topics.leagues.teams') }}",
                    data: { _token: "{{ csrf_token() }}", league_id: leagueId, season_id: seasonId },
                    success: function(data) {
                        let options = '<option value="">....</option>';
                        if (data.ok && data.teams.length > 0) {
                            $.each(data.teams, function(i, team) {
                                options += `<option value="${team.id}">${team.name}</option>`;
                            });
                        }
                        $('#team_id').html(options).trigger('change');
                    },
                    error: function() {
                        $('#team_id').html('<option value="">فشل تحميل الفرق</option>').trigger('change');
                    }
                });
            });

            // Team changes → load only players belonging to the selected team
            $('#team_id').on('change', function() {
                let teamId = $(this).val();

                $('#player_id').html('<option value="">جاري التحميل...</option>').trigger('change');

                if (!teamId) {
                    $('#player_id').html('<option value="">....</option>').trigger('change');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('topics.teams.players') }}",
                    data: { _token: "{{ csrf_token() }}", team_id: teamId },
                    success: function(data) {
                        let options = '<option value="">....</option>';
                        if (data.ok && data.players.length > 0) {
                            $.each(data.players, function(i, player) {
                                options += `<option value="${player.id}">${player.name}</option>`;
                            });
                        }
                        $('#player_id').html(options).trigger('change');
                    },
                    error: function() {
                        $('#player_id').html('<option value="">فشل تحميل اللاعبين</option>').trigger('change');
                    }
                });
            });

        });
    </script>
    @endif

    @if ($WebmasterSection->sportmonks_status == 3)
    <script>
        $(document).ready(function() {

            // Team changes → load matches filtered by league + season + team
            $('#team_id').on('change', function() {
                let leagueId = $('#league_id').val();
                let seasonId = $('#season_id').val();
                let teamId   = $(this).val();

                $('#match_id').html('<option value="">جاري التحميل...</option>').trigger('change');

                if (!teamId) {
                    $('#match_id').html('<option value="">....</option>').trigger('change');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('topics.leagues.team.matches') }}",
                    data: { _token: "{{ csrf_token() }}", league_id: leagueId, season_id: seasonId, team_id: teamId },
                    success: function(data) {
                        let options = '<option value="">....</option>';
                        if (data.ok && data.matches.length > 0) {
                            $.each(data.matches, function(i, match) {
                                options += `<option value="${match.id}">${match.name}</option>`;
                            });
                        }
                        $('#match_id').html(options).trigger('change');
                    },
                    error: function() {
                        $('#match_id').html('<option value="">فشل تحميل المباريات</option>').trigger('change');
                    }
                });
            });

        });
    </script>
    @endif
@endpush
