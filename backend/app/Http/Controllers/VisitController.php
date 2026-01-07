<?php

namespace App\Http\Controllers;
use App\Models\Visit;
use App\Models\VisitsRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class VisitController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        return Inertia::render('Visits/Index', [
            'visits' => Visit::where('user_id', Auth::id())->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Visits/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frame' => 'integer|min:0',
            'time' => 'in:5,10,15,20,30,60,80,100',
            'status' => 'in:on,off,ban',
            'reward' => 'numeric|min:0',
            'cost' => 'numeric|min:0',
            'views' => 'integer|min:0',
            'balance' => 'numeric|min:0',
            'link' => 'required|url|max:255',
            'max_views_per_day' => 'integer|min:0',
            'ip_filter' => 'in:full,mask,off',
        ]);

        $request->user()->visits()->create($validated);

        return redirect()->route('cabinet.visits.index')->with('success', 'Visit created!');
    }

    public function edit(Visit $visit)
    {
        $this->authorize('update', $visit);
        return Inertia::render('Visits/Edit', [
            'visit' => $visit,
        ]);
    }

    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frame' => 'integer|min:0',
            'time' => 'in:5,10,15,20,30,60,80,100',
            'status' => 'in:on,off,ban',
            'reward' => 'numeric|min:0',
            'cost' => 'numeric|min:0',
            'views' => 'integer|min:0',
            'balance' => 'numeric|min:0',
            'link' => 'required|url|max:255',
            'max_views_per_day' => 'integer|min:0',
            'ip_filter' => 'in:full,mask,off',
        ]);

        $visit->update($validated);

        return redirect()->route('cabinet.visits.index')->with('success', 'Visit updated!');
    }

    public function destroy(Visit $visit)
    {
        $this->authorize('delete', $visit); // опционально
        $visit->delete();

        return redirect()->route('cabinet.visits.index')->with('success', 'Visit deleted!');
    }

    public function finish(VisitsRecord $visitsRecord) {
        $visitsRecord->finishVisit();
        return response()->json([
            'status' => 'visit finished',
        ]);
    }
}
