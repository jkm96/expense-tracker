<?php

namespace App\Http\Controllers;

use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
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

        [$success, $message] = $this->authService->verifyUserEmail($token);

        $status = $success ? 'success' : 'error';

        return redirect()->route('login.user')->with($status, $message);
    }
}
