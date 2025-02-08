<?php

namespace App\Http\Controllers;

class CoreController extends Controller
{
    public function home_page()
    {
        return view('core.home-page');
    }

    public function expense_page()
    {
        return view('core.expense-page');
    }
}
