<?php

namespace App\Http\Controllers\Dashboard\Football;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Models\WebmasterSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($country_id = 0)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $countries = \App\Models\Country::where('status', 1)->orderBy('sport_id')->get();
        return view('dashboard.football.teams.list', compact('GeneralWebmasterSections', 'countries', 'country_id'));
    }

    public function create()
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $countries = \App\Models\Country::where('status', 1)->orderBy('sport_id')->get();

        return view('dashboard.football.teams.create', compact('GeneralWebmasterSections', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'local_image' => 'nullable|image|max:2048',
        ]);

        $id = DB::table('teams')->insertGetId([
            'name_ar'    => $request->name_ar,
            'name_en'    => $request->name_en,
            'country_id' => $request->country_id,
            'status'     => 1,
            'row_no'     => (int) Team::max('row_no') + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $team = Team::find($id);

        if ($request->hasFile('local_image')) {
            $file = $request->file('local_image');
            $filename = $team->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->putFileAs('teams', $file, $filename);
            $team->update(['local_image' => $filename]);
        }

        return redirect()->action([TeamsController::class, 'edit'], ['id' => $team->id])->with('doneMessage', __('backend.saveDone'));
    }

    public function list(Request $request)
    {
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $locale = Helper::currentLanguage()->code;
        $name = "name_" . $locale;
        $title = "title_" . $locale;

        $dir = $request->input('order.0.dir', 'asc');
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $q = $request->input('find_q');
        $country_id = $request->input('country_id');

        // أعمدة datatable حسب ترتيبها في JS
        $columns = [
            'id',
            "$name",
            'short_code',
            'country_id',
            'venue_id',
            'founded',
            'status',
            'updated_at',
            'row_no',
        ];

        $order = 'row_no';
        if (!in_array($dir, ['asc', 'desc'])) $dir = 'asc';

        // Query
        $query = \App\Models\Team::query();

        // فلترة بسيطة اختيارية (لو تبغى)
        // $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name_ar', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%");
            });
        }

        if ($country_id > 0) {
            $query->where('country_id', $country_id);
        }

        // status optional
        if ($request->filled('status')) {
            $query->where('status', (int) $request->input('status'));
        }

        $totalData = \App\Models\Team::count();
        $totalFiltered = (clone $query)->count();

        // paginate + order
        $rows = $query->orderBy($order, $dir)
            ->orderBy('row_no', 'asc')
            ->offset($start)
            ->limit($limit > 0 ? $limit : 10)
            ->get();

        $data = [];
        $x = 0;
        $teamsCount = $rows->count();
        foreach ($rows as $team) {
            $x++;
            $logo = $team->image_path
                ? " <div class=\"pull-right\">

                    <img src=\"" . $team->image_path . "?w=150&h=150\" style=\"height: 40px\" alt=\"" . $title . "\"></div>"
                : '-';
            // $logo = $team->image_path
            //     ? '<img src="' . $team->image_path . '" style="width:28px;height:28px;object-fit:contain;border-radius:6px;background:#fff;padding:2px" />'
            //     : '-';
            $statusHtml = "<i class=\"fa " . (($team->status == 1)
                ? "fa-check text-success"
                : "fa-times text-danger") . " inline\"></i>";

            $options = '
                      <div class="dropdown ' . ((($x + 2) >= $teamsCount) ? "dropup" : "") . '">
                    <button type="button" class="btn btn-sm light dk dropdown-toggle" data-toggle="dropdown"><i class="material-icons">&#xe5d4;</i> ' . __('backend.options') . '</button>
                    <div class="dropdown-menu pull-right">';
            if (@Auth::user()->permissionsGroup->edit_status) {
                $options .= '<a class="dropdown-item" href="' . route("teams.edit", [
                    "id" => $team->id
                ]) . '"><i class="material-icons">&#xe3c9;</i> ' . __('backend.edit') . '</a>';
                $options .= '<a class="dropdown-item" href="' . route("update.players", [
                    "id" => $team->id
                ]) . '"><i class="material-icons">&#xe3c9;</i> ' . __('backend.update_players') . '</a>';
            }
            if (@Auth::user()->permissionsGroup->delete_status) {
                $options .= '<a class="dropdown-item text-danger" onclick="DeleteTeam(\'' . $team->id . '\')"><i class="material-icons">&#xe872;</i> ' . __('backend.delete') . '</a>';
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
                'name'       => '<div>' . $team->name_ar . '</div>',
                'name'       => "<input type='text' name=\"row_no_".$team->id."\" id=\"row_no_".$team->id."\" value=\"".$team->row_no."\" class=\"form-control row_no light pull-left\" autocomplete=\"off\"><a href='" . route("teams.edit", [
                    "id" => $team->id
                ]) . "'>" . $logo . "<div class='h6 m-b-0'>" . $team->$name . "</div></a>",
                'country_id' => '<div class="text-center">' . ($team->country->$title ?? '-') . '</div>',
                // 'venue_id'   => '<div class="text-center">' . ($team->venue_id ?? '-') . '</div>',
                // 'founded'    => '<div class="text-center">' . ($team->founded ?? '-') . '</div>',
                'status'     => '<div class="text-center">' . $statusHtml . '</div>',
                'updated_at' => '<div class="text-center">' . $team->updated_at?->format('Y-m-d H:i') . '</div>',
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

    public function edit(Request $request, $id)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $countries = \App\Models\Country::where('status', 1)->orderBy('sport_id')->get();
        $team = Team::findOrFail($id);
        $tab = $request->get('tab', 'details');
        $players = $team->players()
            ->with('country')
            ->orderByDesc('team_players.is_current')
            ->orderBy('team_players.jersey_number')
            ->orderBy('players.name_ar')
            ->get();

        return view('dashboard.football.teams.edit', compact('GeneralWebmasterSections', 'countries', 'team', 'players', 'tab'));
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'status'     => 'required|boolean',
            'is_home'     => 'required|boolean',
            'major_national_teams' => 'required|boolean',
            'local_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name_ar', 'name_en', 'country_id', 'status', 'major_national_teams']);

        if ($request->hasFile('local_image')) {
            if ($team->local_image) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete('teams/' . $team->local_image);
            }
            $file = $request->file('local_image');
            $filename = $team->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->putFileAs('teams', $file, $filename);
            $data['local_image'] = $filename;
        } elseif ($request->input('logo_delete') == '1' && $team->local_image) {
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete('teams/' . $team->local_image);
            $data['local_image'] = null;
        }

        $team->update($data);

        return redirect()->action(
                    [TeamsController::class, 'edit'],
                    ['id' => $id]
                )->with(
                    'doneMessage',
                    __('backend.saveDone')
                );
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return json_encode(array("stat" => "success", "id" => $id));
    }

    public function updateAll(Request $request)
    {
        $ids = $request->input('ids', []);
        // $rowNos = $request->input('row_no', []);

        // if (is_array($ids) && count($ids) > 0) {
            if ($request->action === 'order') {
                foreach ($request->row_ids as $rowId) {
                    $team = Team::find($rowId);
                    if (!empty($team)) {
                        $row_no_val = "row_no_" . $rowId;
                        $team->row_no = $request->$row_no_val;
                        $team->save();
                    }
                }
            }elseif ($request->action === 'delete') {
                 Team::whereIn('id', $ids)->delete();
            } elseif ($request->action === 'activate') {
                Team::whereIn('id', $ids)->update(['status' => 1]);
            } elseif ($request->action === 'block') {
                Team::whereIn('id', $ids)->update(['status' => 0]);
             }
        // }

        return redirect()->action([TeamsController::class, 'index'])->with('doneMessage', __('backend.saveDone'));

    }



    public function editPlayer(Team $team, Player $player)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $countries = \App\Models\Country::where('status', 1)->orderBy('sport_id')->get();
        $tab = 'players';

        $player = $team->players()
            ->with('country')
            ->where('players.id', $player->id)
            ->firstOrFail();

        return view('dashboard.football.teams.players.edit', compact('GeneralWebmasterSections', 'countries', 'team', 'player', 'tab'));
    }

    public function updatePlayer(Request $request, Team $team, Player $player)
    {
        $this->abortIfPlayerNotInTeam($team, $player);

        $request->validate([
            'name_ar'              => 'required|string|max:255',
            'name_en'              => 'nullable|string|max:255',
            'common_name'          => 'nullable|string|max:255',
            'date_of_birth'        => 'nullable|date',
            'gender'               => 'nullable|string|max:20',
            'country_id'           => 'nullable|exists:countries,id',
            'jersey_number'        => 'nullable|integer|min:0',
            'is_current'           => 'required|boolean',
            'local_image'          => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name_ar', 'name_en', 'common_name', 'date_of_birth', 'gender', 'country_id']);

        if ($request->hasFile('local_image')) {
            if ($player->local_image) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete('players/' . $player->local_image);
            }
            $file = $request->file('local_image');
            $filename = $player->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->putFileAs('players', $file, $filename);
            $data['local_image'] = $filename;
        } elseif ($request->input('photo_delete') == '1' && $player->local_image) {
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete('players/' . $player->local_image);
            $data['local_image'] = null;
        }

        $pivotData = [
            'jersey_number' => $request->input('jersey_number'),
            'is_current'    => (bool) $request->input('is_current'),
            'updated_at'    => now(),
        ];

        $player->update($data);

        DB::table('team_players')
            ->where('team_id', $team->id)
            ->where('player_id', $player->id)
            ->update($pivotData);

        return redirect()
            ->route('teams.players.edit', ['team' => $team->id, 'player' => $player->id])
            ->with('doneMessage', __('backend.saveDone'));
    }

    public function destroyPlayer(Team $team, Player $player)
    {
        $this->abortIfPlayerNotInTeam($team, $player);

        DB::table('team_players')
            ->where('team_id', $team->id)
            ->where('player_id', $player->id)
            ->delete();

        return response()->json([
            'stat' => 'success',
            'id'   => $player->id,
        ]);
    }

    private function abortIfPlayerNotInTeam(Team $team, Player $player): void
    {
        $exists = DB::table('team_players')
            ->where('team_id', $team->id)
            ->where('player_id', $player->id)
            ->exists();

        abort_unless($exists, 404);
    }
}
