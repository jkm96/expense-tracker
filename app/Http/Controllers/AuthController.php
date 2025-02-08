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

    public function verify(Request $request)
    {
        $token = $request->query('token');
        return $this->_authService->verifyUserEmail($token);
    }
}
