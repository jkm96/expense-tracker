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

    public function recurring_expense_page()
    {
        return view('core.recurring-expense-page');
    }

    public function settings_page()
    {
        return view('core.settings-page');
    }
}
