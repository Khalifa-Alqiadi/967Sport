<?php

namespace App\Http\Controllers\Dashboard\Football;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Season;
use App\Models\WebmasterSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeasonsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $league_id = null)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $League = League::find($league_id);

        if (!$League) {
            return redirect()
                ->action([LeaguesController::class, 'index'])
                ->with('errorMessage', __('backend.error'));
        }

        $Season = $League->seasons()->where('is_current', 1)->first()
            ?? $League->seasons()->whereKey($League->current_season_id)->first()
            ?? $League->seasons()->orderByDesc('starting_at')->first();
        if (@Auth::user()->permissionsGroup->view_status) {
            $Seasons = Season::where('created_by', '=', Auth::user()->id)
                ->where('league_id', $League->id);
        } else {
            $Seasons = Season::where('league_id', $League->id);
        }
        $search_word = request()->input("q");
        $tab = $request->input('tab', 'seasons');
        if ($search_word != "") {
            $Seasons = $Seasons->where('name', 'like', '%' . $search_word . '%');
        }

        $Seasons = $Seasons->orderby('id', 'desc')->paginate(config('smartend.backend_pagination'));

        return view('dashboard.football.seasons.list', compact('Seasons', 'League', 'tab', 'GeneralWebmasterSections', 'Season'));
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

        return view('dashboard.football.seasons.create', compact('League', 'GeneralWebmasterSections'));
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
            'name'        => 'required|string|max:255',
            'starting_at' => 'nullable|date',
            'ending_at'   => 'nullable|date',
            'is_current'  => 'nullable|boolean',
        ]);

        if ($request->boolean('is_current')) {
            Season::where('league_id', $League->id)->update(['is_current' => false]);
        }

        Season::create([
            'league_id'   => $League->id,
            'name'        => $request->name,
            'starting_at' => $request->starting_at,
            'ending_at'   => $request->ending_at,
            'is_current'  => $request->boolean('is_current'),
        ]);

        return redirect()
            ->action([SeasonsController::class, 'index'], ['league_id' => $League->id, 'tab' => 'seasons'])
            ->with('doneMessage', __('backend.saveDone'));
    }

}
