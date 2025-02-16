<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Component;

class RegisterUser extends Component
{
    public $username;
    public $email;
    public $password;
    public $password_confirmation;
    public $isCreated = false;

    public function render()
    {
        return view('livewire.auth.register-user');
    }

    public function registerUser()
    {
        $this->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => 1
        ]);

        //send email verification message
        $token = Str::random(100);
        $verificationUrl = env('APP_URL') . route('email.verification', ['token' => $token], false);
        UserVerification::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

//        $details = [
//            'type' => EmailTypes::USER_VERIFICATION->name,
//            'recipientEmail' => trim($this->email),
//            'username' => trim($this->username),
//            'verificationUrl' => trim($verificationUrl),
//        ];
//
//        DispatchEmailNotificationsJob::dispatch($details);

        // Flash a success message and reset form fields
        session()->flash('message', 'Account created successfully!');
        $this->reset();

        $this->isCreated = true;
    }

    public function resetForm()
    {
        $this->reset(['username', 'email', 'password', 'password_confirmation']);
        $this->isCreated = false;  // Reset the success message
    }
}
