<?php

namespace App\Http\Controllers;


class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('core.dashboard-page');
    }
}
