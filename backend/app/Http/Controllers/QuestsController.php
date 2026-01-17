<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QuestsController extends Controller
{
    public function index() {
        $quests = Quest::with(['links','limits','targetingTime','targetingProfile','targetingGeo','payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Quests/Index', [
            'quests' => $quests
        ]);
    }
}
