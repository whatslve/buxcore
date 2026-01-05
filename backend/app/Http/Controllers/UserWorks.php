<?php

namespace App\Http\Controllers;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class UserWorks extends Controller
{

    use AuthorizesRequests;

    public function visits()
    {
        $visits = Visit::paginate(10);
        return Inertia::render('Visits', [
            'visits' => $visits,
        ]);
    }

    public function performView(Visit $visit) {
        $user = Auth::user();
//        if($visit->checkIfRecordExists($user, $visit)) {
//            return redirect()->route('visits')->with('error', 'you already done this visit.');
//        }
        $visit->createVisitRecord($user);
        if($visit->frame) {
            return Inertia::render('Visits/IframeViewer', [
                'link' => $visit->link,
                'time' => $visit->time,
                'visit_id' => $visit->id,
            ]);
        } else {
            return Inertia::render('Visits/RedirectToSite', [
               'link' => $visit->link,
               'visit_id' => $visit->id,
            ]);
        }
    }
}
