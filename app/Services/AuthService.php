<?php

namespace App\Services;

use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function verifyUserEmail($token): RedirectResponse
    {
        $userVerify = UserVerification::where('token', $token)->first();
        $message = 'Verification token has expired.';

        if (!is_null($userVerify)) {
            $user = $userVerify->user;
            Log::info(json_encode($user));

            $expirationDate = Carbon::parse($userVerify->created_at)->addDays(7);
            if (Carbon::now()->gt($expirationDate)) {
                $userVerify->delete();

                return redirect()->route('login.user')->with('success',$message);
            }

            if (!$user->is_email_verified) {
                $userVerify->user->is_email_verified = 1;
                $userVerify->user->email_verified_at = Carbon::now();
                $userVerify->user->save();
            }
            $message = "Email verified successfully.";

            return redirect()->route('login.user')->with('success',$message);
        }

        return redirect()->route('login.user')->with('error',$message);
    }
}
