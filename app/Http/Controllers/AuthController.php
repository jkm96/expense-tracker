<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $_authService;

    public function __construct(AuthService $authService)
    {
        $this->_authService = $authService;
    }

    public function register()
    {
        return view('auth.register');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function forgot_password()
    {
        return view('auth.forgot-password');
    }

    public function reset_password(Request $request)
    {
        $token = trim($request->query('token'));
        $email = trim($request->query('email'));
        return view('auth.reset-password',[
            'token' => $token,
            'email' => $email
        ]);
    }

    public function verify(Request $request)
    {
        $token = $request->query('token');
        return $this->_authService->verifyUserEmail($token);
    }

    public function resend_verification(Request $request)
    {
        $token = $request->query('token');
        return $this->_authService->verifyUserEmail($token);
    }
}
