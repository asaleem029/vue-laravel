<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register', 'verifyOtp']]);
    }

    public function login(Request $request)
    {
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
            return $this->respondWithToken($token);
        } else {
            $this->requestOtp($request->get('email'));
            return response()->json(['error' => 'Please Verify Email'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|between:6,255|same:c_password',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $otp = $this->requestOtp($request->get('email'));

        if ($otp) {
            return response()->json([
                'message' => 'User successfully registered',
                'otp' => $otp,
                'user' => $user,
            ], 200);
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
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function requestOtp($email)
    {
        $otp = rand(1000, 9999);
        $user = User::where('email', '=', $email)->update(['otp' => $otp]);

        if ($user) {
            $mail_details = [
                'recipient' => $email,
                'subject' => 'Account Verification OTP',
                'message' => 'Your OTP is : ' . $otp
            ];

            Mail::to($email)->send(new OTPMail($mail_details));

            return true;
        } else {
            return false;
        }
    }

    public function verifyOtp(Request $request)
    {
        $user  = User::where([['email', '=', $request->email], ['otp', '=', $request->otp]])->first();
        if ($user) {
            $token = JWTAuth::fromUser($user);
            $current = now();

            User::where('email', '=', $request->email)->update([
                'otp' => null,
                'email_verified_at' => $current
            ]);

            return response()->json(["status" => 200, "message" => "Success", 'user' => $user, 'access_token' => $token]);
        } else {
            return response()->json(["status" => 401, 'message' => 'Invalid']);
        }
    }
}
