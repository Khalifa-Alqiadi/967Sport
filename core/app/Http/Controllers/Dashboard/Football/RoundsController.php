<?php

namespace App\Http\Controllers\Dashboard\Football;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\FixtureEvent;
use App\Models\League;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use App\Models\WebmasterSection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoundsController extends Controller
{
    private $uploadPath = "leagues";

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function indexOld(Request $request, $league_id, $season_id)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '1')
            ->orderBy('row_no', 'asc')
            ->get();

        $League = League::find($league_id);
        $Season = Season::find($season_id);

        if (!$League) {
            return redirect()
                ->action([LeaguesController::class, 'index'])
                ->with('errorMessage', __('backend.error'));
        }



        /*
        |--------------------------------------------------------------------------
        | 1) جلب stages + rounds + fixtures
        |--------------------------------------------------------------------------
        */
        $stages = $League->stages()
            ->with([
                'rounds' => function ($q) use ($Season) {
                    $q->where('season_id', $Season->id)
                        ->orderBy('starting_at', 'asc');
                },
                'fixtures' => function ($fx) {
                    $fx->orderBy('starting_at', 'asc');
                },
                'fixtures.homeTeam',
                'fixtures.awayTeam',
            ])
            ->where('season_id', $Season->id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('starting_at', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2) بناء قائمة صفحات العرض
        |    - جولات league phase أولاً (1 → 8)
        |    - ثم بقية المراحل
        |--------------------------------------------------------------------------
        */
        $pages = collect();
        $name_var = 'name_' . @Helper::currentLanguage()->code;
        foreach ($stages as $stage) {
            $stageName = mb_strtolower((string) ($stage->name ?? ''));
            if (count($stage->rounds) > 0) {
                // اعتبر هذه المرحلة الأساسية لو عندها عدة جولات
                $isLeaguePhase = $stage->rounds->count() > 1;

                if ($isLeaguePhase) {
                    $roundsCount = $stage->rounds->count();
                    foreach ($stage->rounds as $round) {
                        $pages->push([
                            'type' => 'round',
                            'title' => __('frontend.matchday_progress', [
                                'current' => $round->name,
                                'total' => $roundsCount,
                            ]),
                            'stage' => $stage,
                            'round' => $round,
                            'fixtures' => $round->fixtures,
                        ]);
                    }
                } else {
                    // المراحل الإقصائية كصفحة واحدة
                    $fixtures = $stage->rounds
                        ->flatMap(fn($round) => $round->fixtures)
                        ->sortBy('starting_at')
                        ->values();

                    $pages->push([
                        'type' => 'stage',
                        'title' => $stage->$name_var ?: ('Stage ' . $stage->id),
                        'stage' => $stage,
                        'round' => null,
                        'fixtures' => $fixtures,
                    ]);
                }
            } else {
                $fixtures = Fixture::where('stage_id', $stage->id)
                    ->orderBy('starting_at', 'asc')
                    ->get();
                $pages->push([
                    'type' => 'stage',
                    'title' => $stage->$name_var ?: ('Stage ' . $stage->id),
                    'stage' => $stage,
                    'round' => null,
                    'fixtures' => $fixtures,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3) pagination يدوي
        |--------------------------------------------------------------------------
        */
        $perPage = 1;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $pages->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedPages = new LengthAwarePaginator(
            $currentItems,
            $pages->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 4) انتقال تلقائي للجولة الحالية
        |--------------------------------------------------------------------------
        */
        if (!$request->has('page')) {
            $targetIndex = 0;
            foreach ($pages as $index => $page) {
                if ($page['type'] === 'round' && $page['round'] && $page['round']->is_current) {
                    $targetIndex = $index;
                    break;
                }
            }
            foreach ($pages as $index => $page) {

                if ($page['type'] === 'stage' && $page['stage'] && $page['stage']->is_current && $targetIndex <= 0) {
                    $targetIndex = $index;
                    break;
                }
            }

            $targetPage = $targetIndex + 1;

            return redirect()->route('leaguesRounds', [
                'league_id' => $League->id,
                'season_id' => $Season->id,
                'page' => $targetPage,
            ]);
        }

        $tab = $request->input('tab', 'matches');

        return view('dashboard.football.rounds.list', compact(
            'League',
            'GeneralWebmasterSections',
            'Season',
            'stages',
            'paginatedPages',
            'tab'
        ));
    }
    public function index(Request $request, $league_id)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '1')
            ->orderBy('row_no', 'asc')
            ->get();

        $League = League::find($league_id);

        if (!$League) {
            return redirect()
                ->action([LeaguesController::class, 'index'])
                ->with('errorMessage', __('backend.error'));
        }

        $seasons = $League->seasons()->orderBy('starting_at', 'desc')->get();



        $tab = $request->input('tab', 'matches');

        return view('dashboard.football.rounds.list', compact(
            'League',
            'GeneralWebmasterSections',
            'tab',
            'seasons'
        ));
    }

    public function list(Request $request, $league_id)
    {
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $locale = Helper::currentLanguage()->code;
        $name = "name_" . $locale;
        $title = "title_" . $locale;

        $dir = $request->input('order.0.dir', 'desc');
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $q = $request->input('find_q');
        $season_id = $request->input('season_id');
        $find_date = $request->input('date');
        $find_status = $request->input('status');
        $find_group_id = $request->input('group_id');

        if (!$season_id) {
            $season_id = Season::where('is_current', 1)
                ->where('league_id', $league_id)
                ->value('id');

            $season_id ??= League::whereKey($league_id)->value('current_season_id');
            $season_id ??= Season::where('league_id', $league_id)
                ->orderByDesc('starting_at')
                ->value('id');
        }
        $matchesQuery = Fixture::where('season_id', $season_id)
            ->where('league_id', $league_id)
            ->with(['homeTeam', 'awayTeam']);

        if ($find_date) {
            $matchesQuery = $matchesQuery->whereDate('starting_at', $find_date);
        }
        if ($find_status) {
            if ($find_status === 'not_started') {
                $matchesQuery = $matchesQuery->where(function ($q) {
                    $q->where('is_finished', 0);
                });
            } elseif ($find_status === 'live') {
                $matchesQuery = $matchesQuery->where(function ($q) {
                    $q->where('is_finished', 0)
                        ->where('starting_at', '<=', now());
                });
            } elseif ($find_status === 'finished') {
                $matchesQuery = $matchesQuery->where('is_finished', 1);
            }
        }
        if ($find_group_id) {
            $matchesQuery = $matchesQuery->where('group_id', (int) $find_group_id);
        }

        // أعمدة datatable حسب ترتيبها في JS
        $columns = [
            0 => 'id',
            1 => 'id',
            2 => 'starting_at',
            3 => 'starting_at',
            4 => 'is_finished',
        ];

        $order = $columns[$orderColumnIndex] ?? 'starting_at';
        if (!in_array($dir, ['asc', 'desc'])) $dir = 'desc';

        // فلترة بسيطة اختيارية (لو تبغى)
        // $q = trim((string) $request->input('q', ''));
        // if ($q !== '') {
        //     $matchesQuery->where(function ($qq) use ($q) {
        //         $qq->where('name_ar', 'like', "%{$q}%")
        //             ->orWhere('name_en', 'like', "%{$q}%");
        //     });
        // }





        $totalData = $matchesQuery->count();
        $totalFiltered = (clone $matchesQuery)->count();

        // paginate + order
        if ($limit > 0) {
            $matchesQuery = $matchesQuery->offset($start)->limit($limit);
        }
        $rows = $matchesQuery->orderBy($order, $dir)
            ->orderBy('starting_at', 'asc')
            ->get();

        $data = [];
        $x = 0;
        $matchsCount = $rows->count();
        foreach ($rows as $team) {
            $x++;
            $homeTeam = $team->homeTeam;
            $awayTeam = $team->awayTeam;
            $homeName = $homeTeam ? ($homeTeam->{$name} ?? $homeTeam->name_ar ?? '-') : '-';
            $awayName = $awayTeam ? ($awayTeam->{$name} ?? $awayTeam->name_ar ?? '-') : '-';

            $logo = '<a href="' . route('matcheRoundsEdit', ['id' => $team->id]) . '"
                    class="d-flex justify-content-between"
                    style="justify-content: space-between; display:flex">
                    <div>';
            if ($homeTeam || $awayTeam) {
                if ($homeTeam && $homeTeam->image_path) {
                    $logo .= '<img src="' . e($homeTeam->image_path) . '"
                        style="height:30px; margin: 0 4px;" alt="">';
                }
                $logo .= '<span>' . e($homeName) . '</span></div>';
                $logo .= '<span class="m-x-sm">vs</span>';
                $logo .= '<div>';
                if ($awayTeam) {
                    if ($awayTeam->image_path) {
                        $logo .= '<img src="' . e($awayTeam->image_path) . '"
                            style="height:30px; margin: 0 4px;" alt="">';
                    }
                    $logo .= '<span>' . e($awayName) . '</span>';
                }
                $logo .= '</div>';
            }
            $logo .= '</a>';
            $starting_at = "<div class='text-center'>" . ($team->starting_at ? Carbon::parse($team->starting_at)->format('Y-m-d H:i') : '-') . "</div>";
            $is_finished = "<div class='text-center'>";
            if ($team->is_finished)
                $is_finished .= '<span class="text-info">' . __('backend.finished') . '</span>';
            elseif ($team->starting_at && $team->starting_at > now())
                $is_finished .= '<span class="text-success">' . __('backend.not_started_yet') . '</span>';
            else
                $is_finished .= '<span class="text-warning">' . __('backend.live_now') . '</span>';
            $is_finished .= "</div>";

            $options = '
                      <div class="dropdown ' . ((($x + 2) >= $matchsCount) ? "dropup" : "") . '">
                    <button type="button" class="btn btn-sm light dk dropdown-toggle" data-toggle="dropdown"><i class="material-icons">&#xe5d4;</i> ' . __('backend.options') . '</button>
                    <div class="dropdown-menu pull-right">';
            if (@Auth::user()->permissionsGroup->edit_status) {
                $options .= '<a class="dropdown-item" href="' . route("matcheRoundsEdit", [
                    "id" => $team->id
                ]) . '"><i class="material-icons">&#xe3c9;</i> ' . __('backend.edit') . '</a>';
            }
            $options .= '</div></div>';

            $data[] = [
                'check' => "<div class='row_checker'><label class=\"ui-check m-a-0\">
                            <input type=\"checkbox\" name=\"ids[]\" value=\"" . $team->id . "\"><i
                                    class=\"dark-white\"></i>
                                    <input type='hidden' name='row_ids[]' value='" . $team->id . "' class='form-control row_no'>
                        </label>
                    </div>",
                'id'         => '<div class="text-center">' . $team->id . '</div>',
                // 'logo'       => '<div class="text-center">' . $logo . '</div>',
                'title'       => $logo,
                'starting_at' => $starting_at,
                'is_finished' => $is_finished,
                'options'    => "<div class='text-center'>" . $options . "</div>",
            ];
        }

        return response()->json([
            "draw"            => (int) $request->input('draw'),
            "recordsTotal"    => (int) $totalData,
            "recordsFiltered" => (int) $totalFiltered,
            "data"            => $data,
        ]);
    }

    public function matchesUpdateAllAPI(Request $request)
    {
        $ids = collect($request->input('ids', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->back()->with('errorMessage', __('backend.error'));
        }

        if ($request->input('action') === 'mark_finished') {
            Fixture::whereIn('id', $ids)->update([
                'is_finished' => 1,
                'minute' => null,
            ]);
        }

        return redirect()->back()->with('doneMessage', __('backend.saveDone'));
    }

    public function matcheRoundsEdit($id)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $match = Fixture::with([
            'league',
            'season',
            'homeTeam',
            'awayTeam',
            'events.player',
        ])->find($id);

        $today = Carbon::now()->addMinutes(110);


        if (!empty($match)) {
            $matchStatuses = $this->matchStatuses();
            $homePlayers = $this->teamPlayersForFixture($match, (int) $match->home_team_id);
            $awayPlayers = $this->teamPlayersForFixture($match, (int) $match->away_team_id);

            return view('dashboard.football.rounds.details', compact(
                'match',
                'GeneralWebmasterSections',
                'today',
                'matchStatuses',
                'homePlayers',
                'awayPlayers'
            ));
        } else {
            return redirect()->action([RoundsController::class, 'index'])->with('doneMessage', __('backend.saveDone'));
        }
    }

    public function matchUpdate(Request $request, $id)
    {
        $fixture = Fixture::findOrFail($id);

        $statusCodes = array_keys($this->matchStatuses());
        $validated = $request->validate([
            'starting_at' => ['required', 'date'],
            'state_code' => ['required', 'in:'.implode(',', $statusCodes)],
            'minute' => ['nullable', 'integer', 'min:0', 'max:180'],
            'first_half_added_time' => ['nullable', 'integer', 'min:0', 'max:60'],
            'second_half_added_time' => ['nullable', 'integer', 'min:0', 'max:60'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'ht_home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'ht_away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'ft_home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'ft_away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'et_home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'et_away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'pen_home' => ['nullable', 'integer', 'min:0', 'max:99'],
            'pen_away' => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $stateNames = [
            'NS' => 'Not Started', '1H' => 'First Half', 'HT' => 'Half Time',
            '2H' => 'Second Half', 'LIVE' => 'Live', 'BREAK' => 'Break',
            'ET' => 'Extra Time', 'FT' => 'Finished', 'AET' => 'After Extra Time',
            'PEN' => 'Penalties', 'PST' => 'Postponed', 'CANC' => 'Cancelled',
            'ABD' => 'Abandoned', 'DELAYED' => 'Delayed', 'AWARDED' => 'Awarded',
        ];

        $finishedStatuses = ['FT', 'AET', 'PEN', 'AWARDED'];
        $validated['state_name'] = $stateNames[$validated['state_code']];
        $validated['is_finished'] = in_array($validated['state_code'], $finishedStatuses, true);
        $validated['is_home'] = $request->boolean('is_home');
        $validated['is_slider'] = $request->boolean('is_slider');

        $hasGoalEvents = $fixture->events()->goals()->where('rescinded', false)->exists();
        if ($hasGoalEvents) {
            unset($validated['home_score'], $validated['away_score']);
        }

        DB::transaction(function () use ($fixture, $validated, $hasGoalEvents): void {
            $fixture->update($validated);
            if ($hasGoalEvents) {
                $this->recalculateFixtureScore($fixture->fresh());
            }
        });

        return redirect()->action([RoundsController::class, 'matcheRoundsEdit'], ['id' => $id])->with('doneMessage', __('backend.saveDone'));
    }

    public function storeGoal(Request $request, Fixture $fixture)
    {
        $validated = $this->validateGoalEvent($request, $fixture);

        DB::transaction(function () use ($fixture, $validated): void {
            $lockedFixture = Fixture::query()->lockForUpdate()->findOrFail($fixture->id);

            FixtureEvent::create([
                'fixture_id' => $lockedFixture->id,
                'team_id' => $validated['team_id'],
                'player_id' => $validated['player_id'],
                'type' => FixtureEvent::TYPE_GOAL,
                'type_id' => 14,
                'minute' => $validated['minute'],
                'extra_minute' => $validated['extra_minute'] ?? null,
                'sort_order' => (int) FixtureEvent::where('fixture_id', $lockedFixture->id)->max('sort_order') + 1,
                'payload' => ['source' => 'dashboard', 'created_by' => Auth::id()],
            ]);

            $this->recalculateFixtureScore($lockedFixture);
        });

        return redirect()
            ->to(route('matcheRoundsEdit', ['id' => $fixture->id]).'#match-goals')
            ->with('doneMessage', __('backend.matchGoalAdded'));
    }

    public function updateGoal(Request $request, Fixture $fixture, FixtureEvent $event)
    {
        $this->ensureGoalBelongsToFixture($fixture, $event);
        $validated = $this->validateGoalEvent($request, $fixture);

        DB::transaction(function () use ($fixture, $event, $validated): void {
            $lockedFixture = Fixture::query()->lockForUpdate()->findOrFail($fixture->id);
            $event->update([
                'team_id' => $validated['team_id'],
                'player_id' => $validated['player_id'],
                'minute' => $validated['minute'],
                'extra_minute' => $validated['extra_minute'] ?? null,
            ]);
            $this->recalculateFixtureScore($lockedFixture);
        });

        return redirect()
            ->to(route('matcheRoundsEdit', ['id' => $fixture->id]).'#match-goals')
            ->with('doneMessage', __('backend.matchGoalUpdated'));
    }

    public function destroyGoal(Fixture $fixture, FixtureEvent $event)
    {
        $this->ensureGoalBelongsToFixture($fixture, $event);

        DB::transaction(function () use ($fixture, $event): void {
            $lockedFixture = Fixture::query()->lockForUpdate()->findOrFail($fixture->id);
            $event->delete();
            $this->recalculateFixtureScore($lockedFixture);
        });

        return redirect()
            ->to(route('matcheRoundsEdit', ['id' => $fixture->id]).'#match-goals')
            ->with('doneMessage', __('backend.matchGoalDeleted'));
    }

    private function validateGoalEvent(Request $request, Fixture $fixture): array
    {
        $validator = Validator::make($request->all(), [
            'team_id' => ['required', 'integer', 'in:'.$fixture->home_team_id.','.$fixture->away_team_id],
            'player_id' => ['required', 'integer', 'exists:players,id'],
            'minute' => ['required', 'integer', 'min:0', 'max:180'],
            'extra_minute' => ['nullable', 'integer', 'min:0', 'max:60'],
        ]);

        $validator->after(function ($validator) use ($request, $fixture): void {
            if ($validator->errors()->has('team_id') || $validator->errors()->has('player_id')) {
                return;
            }

            $isLinked = DB::table('team_players')
                ->where('team_id', (int) $request->input('team_id'))
                ->where('player_id', (int) $request->input('player_id'))
                ->where(function ($query) use ($fixture): void {
                    if ($fixture->season_id) {
                        $query->where('season_id', $fixture->season_id)->orWhere('is_current', true);
                    } else {
                        $query->where('is_current', true);
                    }
                })
                ->exists();

            if (!$isLinked) {
                $validator->errors()->add('player_id', __('backend.matchGoalPlayerTeamMismatch'));
            }
        });

        return $validator->validate();
    }

    private function ensureGoalBelongsToFixture(Fixture $fixture, FixtureEvent $event): void
    {
        abort_unless(
            (int) $event->fixture_id === (int) $fixture->id
            && in_array($event->type, [FixtureEvent::TYPE_GOAL, FixtureEvent::TYPE_OWN_GOAL, FixtureEvent::TYPE_PENALTY], true),
            404
        );
    }

    private function teamPlayersForFixture(Fixture $fixture, int $teamId)
    {
        return Player::query()
            ->whereExists(function ($query) use ($fixture, $teamId): void {
                $query->selectRaw('1')
                    ->from('team_players')
                    ->whereColumn('team_players.player_id', 'players.id')
                    ->where('team_players.team_id', $teamId)
                    ->where(function ($membership) use ($fixture): void {
                        if ($fixture->season_id) {
                            $membership->where('team_players.season_id', $fixture->season_id)
                                ->orWhere('team_players.is_current', true);
                        } else {
                            $membership->where('team_players.is_current', true);
                        }
                    });
            })
            ->orderBy('players.name_ar')
            ->get();
    }

    private function recalculateFixtureScore(Fixture $fixture): void
    {
        $goals = FixtureEvent::query()
            ->where('fixture_id', $fixture->id)
            ->goals()
            ->where('rescinded', false)
            ->lockForUpdate()
            ->get()
            ->sortBy(fn(FixtureEvent $event) => sprintf(
                '%03d-%03d-%020d',
                $event->minute ?? 999,
                $event->extra_minute ?? 0,
                $event->id
            ));

        $homeScore = 0;
        $awayScore = 0;
        $sortOrder = 1;

        foreach ($goals as $goal) {
            if ((int) $goal->team_id === (int) $fixture->home_team_id) {
                $homeScore++;
            } elseif ((int) $goal->team_id === (int) $fixture->away_team_id) {
                $awayScore++;
            }

            $goal->update([
                'sort_order' => $sortOrder++,
                'result' => $homeScore.'-'.$awayScore,
            ]);
        }

        $scores = ['home_score' => $homeScore, 'away_score' => $awayScore];
        if ($fixture->is_finished) {
            $scores['ft_home_score'] = $homeScore;
            $scores['ft_away_score'] = $awayScore;
        }

        $fixture->update($scores);
    }

    public function create($league_id)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $League = League::find($league_id);

        if (!$League) {
            return redirect()
                ->action([LeaguesController::class, 'index'])
                ->with('errorMessage', __('backend.error'));
        }

        $seasons = $League->seasons()->orderBy('starting_at', 'desc')->get();
        $teams = Team::where('status', 1)->orderBy('name_ar')->get();
        $venues = Venue::orderBy('name_ar')->get();

        return view('dashboard.football.rounds.create', compact('League', 'GeneralWebmasterSections', 'seasons', 'teams', 'venues'));
    }

    public function store(Request $request, $league_id)
    {
        $League = League::find($league_id);

        if (!$League) {
            return redirect()
                ->action([LeaguesController::class, 'index'])
                ->with('errorMessage', __('backend.error'));
        }

        $request->validate([
            'season_id'    => 'required|exists:seasons,id',
            'home_team_id' => 'required|exists:teams,id|different:away_team_id',
            'away_team_id' => 'required|exists:teams,id',
            'venue_id'     => 'nullable|exists:venues,id',
            'starting_at'  => 'required|date',
        ]);

        $fixture = Fixture::create([
            'league_id'    => $League->id,
            'season_id'    => $request->season_id,
            'venue_id'     => $request->venue_id,
            'home_team_id' => $request->home_team_id,
            'away_team_id' => $request->away_team_id,
            'starting_at'  => $request->starting_at,
            'state_name'   => 'Not Started',
            'state_code'   => 'NS',
            'is_finished'  => false,
        ]);

        return redirect()
            ->action([RoundsController::class, 'index'], ['league_id' => $League->id])
            ->with('doneMessage', __('backend.saveDone'));
    }

    private function matchStatuses(): array
    {
        return [
            'NS' => __('backend.matchStatusNotStarted'),
            '1H' => __('backend.matchStatusFirstHalf'),
            'HT' => __('backend.matchStatusHalfTime'),
            '2H' => __('backend.matchStatusSecondHalf'),
            'LIVE' => __('backend.matchStatusLive'),
            'BREAK' => __('backend.matchStatusBreak'),
            'ET' => __('backend.matchStatusExtraTime'),
            'FT' => __('backend.matchStatusFinished'),
            'AET' => __('backend.matchStatusAfterExtraTime'),
            'PEN' => __('backend.matchStatusPenalties'),
            'PST' => __('backend.matchStatusPostponed'),
            'CANC' => __('backend.matchStatusCancelled'),
            'ABD' => __('backend.matchStatusAbandoned'),
            'DELAYED' => __('backend.matchStatusDelayed'),
            'AWARDED' => __('backend.matchStatusAwarded'),
        ];
    }
}
