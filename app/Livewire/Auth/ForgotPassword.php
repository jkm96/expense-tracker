<?php

namespace App\Livewire\Auth;

use App\Jobs\DispatchEmailNotificationsJob;
use App\Models\User;
use App\Services\Auth\AuthServiceInterface;
use App\Utils\Constants\AppEmailType;
use App\Utils\Constants\AppEventListener;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email;
    public $message;

    public function sendResetLink(AuthServiceInterface $authService)
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $authService->sendPasswordResetLink($this->email);

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
            'message' => 'A password reset link has been sent to your email',
            'type' => 'success'
        ]);

        $this->reset(['email']);

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
