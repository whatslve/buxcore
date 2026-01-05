<?php

namespace App\Http\Controllers;

use App\Models\VisitsRecord;

class ApiController extends Controller
{
    public function finishVisit(VisitsRecord $visitsRecord)
    {
        $visitsRecord->finishVisit();
        return response()->json([
            'status' => 'visit finished',
        ]);
    }
}
