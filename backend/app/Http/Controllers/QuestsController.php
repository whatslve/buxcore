<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class QuestsController extends Controller
{
    public function index() {
        return Inertia::render('Quests/Index', [

        ]);
    }
}
