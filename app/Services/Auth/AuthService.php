<?php

namespace App\Services\Auth;

use App\Jobs\DispatchEmailNotificationsJob;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserVerification;
use App\Utils\Constants\AppEmailType;
use App\Utils\Enums\AuditAction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    public function registerUser(array $data): User
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => 1,
        ]);

        $token = Str::random(100);
        UserVerification::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        $verificationUrl = route('email.verification', ['token' => $token]);

        DispatchEmailNotificationsJob::dispatch([
            'type' => AppEmailType::USER_VERIFICATION,
            'recipientEmail' => $user->email,
            'username' => $user->username,
            'verificationUrl' => $verificationUrl,
        ]);

        return $user;
    }

    public function verifyUserEmail($token): array
    {
        $verification = UserVerification::where('token', $token)->first();

        if (!$verification) {
            return [false, 'Verification token has expired.'];
        }

        $user = $verification->user;
        $expired = Carbon::parse($verification->created_at)->addDays(7)->lt(Carbon::now());

        if ($expired) {
            $verification->delete();
            return [false, 'Verification token has expired.'];
        }

        if (!$user->is_email_verified) {
            $user->update([
                'is_email_verified' => true,
                'email_verified_at' => Carbon::now(),
            ]);
        }

        return [true, 'message' => 'Email verified successfully.'];
    }

    public function login(string $identifier, string $password): bool
    {
        $credentials = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $identifier, 'password' => $password, 'is_email_verified' => 1, 'is_active' => 1]
            : ['username' => $identifier, 'password' => $password, 'is_email_verified' => 1, 'is_active' => 1];

        if (!Auth::attempt($credentials)) {
            return false;
        }

        session()->regenerate();

        $userId = Auth::id();
        AuditLog::log(
            AuditAction::AUTH,
            $identifier,
            $userId,
            'User logged in successfully',
            'User',
            $userId,
            ['identifier' => $identifier, 'ip' => request()->ip()]
        );

        return true;
    }

    public function logoutUser(): void
    {
        $user = Auth::user();

        AuditLog::log(
            AuditAction::AUTH,
            $user->username,
            $user->id,
            'User logged out successfully',
            'User',
            $user->id,
            ['identifier' => $user->username, 'ip' => request()->ip()]
        );

        Auth::logout();
        Session::flush();
    }

    public function resetPassword(array $data): bool
    {
        $resetEntry = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->where('token', $data['token'])
            ->first();

        if (!$resetEntry) {
            return false;
        }

        User::where('email', $data['email'])->update([
            'password' => Hash::make($data['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return true;
    }

    public function sendPasswordResetLink(string $email): void
    {
        $email = trim($email);
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Fake a delay and exit early for security
            return;
        }

        $token = Str::random(164);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $resetUrl = route('reset.password', ['email' => $email, 'token' => $token]);
        Log::info($resetUrl);

        $details = [
            'type' => AppEmailType::USER_FORGOT_PASSWORD,
            'recipientEmail' => $email,
            'username' => $user->username,
            'resetPassUrl' => $resetUrl,
        ];

        DispatchEmailNotificationsJob::dispatch($details);
    }

}
