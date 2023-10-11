<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        // Validate request
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = Auth::attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if ($user->email_verified_at !== NULL) {
            // If email is verified return jwt token
            return $this->respondWithToken($token);
        } else {
            // If email is not verified send OTP to users email
            $this->requestOtp($request->get('email'));
            return response()->json(['error' => 'Please Verify Email'], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout($request);
        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }

    protected function respondWithToken($token)
    {
        // Respond JWT token
        return response()->json([
            'access_token' => $token, 
            'token_type' => 'bearer', // Set token type
            'expires_in' => Auth::factory()->getTTL() * 60 // set expiry time
        ]);
    }
}
