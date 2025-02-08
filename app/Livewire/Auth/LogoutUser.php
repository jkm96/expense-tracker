<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LogoutUser extends Component
{
    public function logout()
    {
        // Perform the logout
        Auth::logout();

        // Clear the session
        Session::flush();

        // Redirect the user after logging out
        return redirect()->route('login.user')->with('success', 'You have been logged out successfully.');
    }

    public function render()
    {
        return view('livewire.auth.logout-user');
    }
}
