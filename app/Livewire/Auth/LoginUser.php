<?php

namespace App\Livewire\Auth;

use App\Notifications\ExpenseReminderNotification;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\SessionHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LoginUser extends Component
{
    public $identifier; // Username or email
    public $password;

    public function loginUser()
    {
        $this->validate([
            'identifier' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // Attempt login with email or username
        $credentials = filter_var($this->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->identifier, 'password' => $this->password, 'is_email_verified' => 1, 'is_active' => 1]
            : ['username' => $this->identifier, 'password' => $this->password, 'is_email_verified' => 1, 'is_active' => 1];

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            session()->flash('success', 'Welcome, ' . $this->identifier);

            return redirect()->route('user.dashboard');
        }

        $this->addError('identifier', 'Invalid credentials. Please try again.');
        session()->flash('error', "Invalid credentials");

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.auth.login-user');
    }
}
