<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AdvertDashboard extends Controller
{
    public function index() {
        return Inertia::render('Adv_dashboard');
    }
}
