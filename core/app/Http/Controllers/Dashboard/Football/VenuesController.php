<?php

namespace App\Http\Controllers\Dashboard\Football;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\WebmasterSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenuesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', 1)->orderBy('row_no')->get();

        if ($request->ajax()) {
            return $this->dataTable($request);
        }

        return view('dashboard.football.venues.list', compact('GeneralWebmasterSections'));
    }

    private function dataTable(Request $request)
    {
        $columns = ['id', 'name_ar', 'name_en', 'city_name', 'capacity'];
        $nameVar = 'name_' . (Helper::currentLanguage()->code ?? 'ar');

        $query = Venue::query();

        $search = $request->input('search.value', '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('name_en', 'LIKE', "%{$search}%")
                  ->orWhere('city_name', 'LIKE', "%{$search}%");
            });
        }

        $totalData = Venue::count();
        $totalFiltered = $query->count();

        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);

        $venues = $query->orderBy($orderCol, $orderDir)->skip($start)->take($length)->get();

        $data = [];
        foreach ($venues as $venue) {
            $img = $venue->image_path
                ? '<img src="' . e($venue->image_path) . '" style="height:32px;width:32px;object-fit:cover;border-radius:6px;margin-inline-end:8px">'
                : '';

            $data[] = [
                $venue->id,
                $img . e($venue->$nameVar ?: $venue->name_en ?: $venue->name_ar),
                e($venue->city_name ?? '-'),
                $venue->capacity ? number_format($venue->capacity) : '-',
                '<a href="' . route('venues.edit', ['id' => $venue->id]) . '" class="btn btn-sm btn-info"><i class="material-icons">&#xe3c9;</i></a>',
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $GeneralWebmasterSections = WebmasterSection::where('status', 1)->orderBy('row_no')->get();
        $venue = Venue::findOrFail($id);

        return view('dashboard.football.venues.edit', compact('GeneralWebmasterSections', 'venue'));
    }

    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'city_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'capacity' => 'nullable|integer|min:0',
            'surface' => 'nullable|string|max:100',
            'local_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name_ar', 'name_en', 'city_name', 'address', 'capacity', 'surface']);

        if ($request->hasFile('local_image')) {
            if ($venue->local_image) {
                Storage::disk(config('filesystems.default'))->delete('venues/' . $venue->local_image);
            }
            $file = $request->file('local_image');
            $filename = $venue->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            Storage::disk(config('filesystems.default'))->putFileAs('venues', $file, $filename);
            $data['local_image'] = $filename;
        } elseif ($request->input('image_delete') == '1' && $venue->local_image) {
            Storage::disk(config('filesystems.default'))->delete('venues/' . $venue->local_image);
            $data['local_image'] = null;
        }

        $venue->update($data);

        return redirect()->route('venues.edit', ['id' => $id])->with('doneMessage', __('backend.saveDone'));
    }

}
