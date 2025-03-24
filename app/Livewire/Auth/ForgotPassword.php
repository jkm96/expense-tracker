<?php

namespace App\Livewire\Auth;

use App\Jobs\DispatchEmailNotificationsJob;
use App\Models\User;
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

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email',
        ]);
        $userEmail = trim($this->email);
        $userExists = User::where('email', $userEmail)->first();

        $message = 'A password reset link has been sent to your email';
        if ($userExists == null) {
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => $message, 'type' => 'success']);
            $this->reset(['email']);

            return redirect()->back();
        }

        $token = Str::random(164);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $resetPassUrl = route('reset.password', ['email' => $userEmail, 'token' => $token]);
        Log::info($resetPassUrl);
        $details = [
            'type' => AppEmailType::USER_FORGOT_PASSWORD,
            'recipientEmail' => trim($userEmail),
            'username' => trim($userExists->username),
            'resetPassUrl' => trim($resetPassUrl),
        ];

        DispatchEmailNotificationsJob::dispatch($details);

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => $message, 'type' => 'success']);
        $this->reset(['email']);

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
