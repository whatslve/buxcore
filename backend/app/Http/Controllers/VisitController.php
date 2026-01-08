<?php

namespace App\Http\Controllers;
use App\Models\Visit;
use App\Models\VisitsRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;
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

    public function finish(Request $request, VisitsRecord $visitsRecord)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['message' => 'Token is required'], 422);
        }

        // Проверка reCAPTCHA через Google
        $secret = config('services.recaptcha.secret');
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            //'remoteip' => $request->ip(), // опционально
        ]);

        $body = $response->json();

        if (!isset($body['success']) || $body['success'] !== true) {
            return response()->json(['message' => 'reCAPTCHA verification failed'], 422);
        }

        // Проверяем score v3 (0.0-1.0)
        if (isset($body['score']) && $body['score'] < 0.5) {
            return response()->json(['message' => 'reCAPTCHA score too low'], 422);
        }

        // Всё ок — завершаем визит
        $visitsRecord->finishVisit();

        return response()->json([
            'status' => 'visit finished',
            'recaptcha_score' => $body['score'] ?? null,
        ]);
    }

}
