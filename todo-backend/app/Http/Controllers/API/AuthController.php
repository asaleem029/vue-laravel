<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('jwt.verify', ['except' => ['login', 'register', 'verifyOtp']]);
    }

    public function register(Request $request)
    {
        return $this->authService->register($request);
    }

    public function verifyOtp(Request $request)
    {
        return $this->authService->verifyOtp($request);
    }

    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    public function logout(Request $request)
    {
        Auth::logout($request);
        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }
}
