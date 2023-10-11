<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\OTPMail;
use Exception;
use JWTAuth;

class AuthService
{
    public function register($data)
    {
        try {
            // Validate request
            $validator = Validator::make($data->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|between:6,255|same:c_password',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            // Create new user
            $user = User::create([
                'name' => $data->get('name'),
                'email' => $data->get('email'),
                'password' => Hash::make($data->get('password')),
            ]);

            if ($user) {
                // Call api to send OTP to users email
                $otp = $this->requestOtp($data->get('email'));

                if ($otp) {
                    return response()->json([
                        'message' => 'User successfully registered',
                        'otp' => $otp,
                        'user' => $user,
                    ], 200);
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function requestOtp($email)
    {
        try {
            $otp = rand(1000, 9999); // Generate OTP
            $user = User::where('email', '=', $email)->update(['otp' => $otp]); // Update OTP field against users email

            if ($user) {
                // Creaing mail data
                $mail_details = [
                    'recipient' => $email,
                    'subject' => 'Account Verification OTP',
                    'message' => 'Your OTP is : ' . $otp
                ];

                // Send mail to users email
                Mail::to($email)->send(new OTPMail($mail_details));

                return true;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function verifyOtp($data)
    {
        try {
            // Validate request
            $validator = Validator::make($data->all(), [
                'email' => 'required',
                'otp' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            // Verify users email and OTP
            $user  = User::where([['email', '=', $data->email], ['otp', '=', $data->otp]])->first();

            if ($user) {
                // Set auth user and create JWT token
                $token = JWTAuth::fromUser($user);
                $current = now();

                // Set otp to null and update email verification field
                User::where('email', '=', $data->email)->update([
                    'otp' => null,
                    'email_verified_at' => $current
                ]);

                // Return response with JWT token
                return response()->json(["status" => 200, "message" => "Success", 'user' => $user, 'access_token' => $token]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function login($data)
    {
        try {
            // Validate request
            $validator = Validator::make($data->all(), [
                'email' => 'required|email',
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
                $this->requestOtp($data->get('email'));
                return response()->json(['message' => 'Please Verify Email'], 200);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function respondWithToken($token)
    {
        try {
            // Respond JWT token
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer', // Set token type
                'expires_in' => Auth::factory()->getTTL() * 60 // set expiry time
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
