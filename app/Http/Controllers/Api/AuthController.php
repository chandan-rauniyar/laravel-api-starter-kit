<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        // Check if user already exists
        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'User already exists with this email.'
            ], 409);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Send verification OTP
        $this->sendVerificationOTP($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'message' => 'Registration successful. Please verify your email address.'
        ], 201);
    }

    public function sendEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $this->sendVerificationOTP($user);

        return response()->json(['message' => 'Verification OTP sent to your email.']);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $otpRecord = DB::table('email_verification_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        // Verify the email
        $user->email_verified_at = now();
        $user->save();

        // Delete the OTP
        DB::table('email_verification_otps')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    private function sendVerificationOTP(User $user)
    {
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        DB::table('email_verification_otps')->updateOrInsert(
            ['email' => $user->email],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Send verification OTP via email
        $html = "
            <h2>Email Verification</h2>
            <p>Hello {$user->name},</p>
            <p>Your email verification OTP is: <strong>{$otp}</strong></p>
            <p>This OTP is valid for 10 minutes.</p>
            <p>If you didn't request this verification, please ignore this email.</p>
        ";

        Mail::send([], [], function ($message) use ($user, $html) {
            $message->to($user->email)
                ->subject('Verify Your Email Address')
                ->html($html);
        });
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $otpRecord = DB::table('otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        // Generate a secure reset token
        $resetToken = Str::random(64);
        DB::table('otps')->where('email', $request->email)->update([
            'otp' => null, // Invalidate OTP
            'reset_token' => $resetToken,
            'reset_token_expires_at' => now()->addMinutes(15),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'OTP verified. Use the reset token to set a new password.',
            'reset_token' => $resetToken
        ]);
    }

    public function setNewPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reset_token' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $otpRecord = DB::table('otps')
            ->where('email', $request->email)
            ->where('reset_token', $request->reset_token)
            ->where('reset_token_expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired reset token.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Delete OTP/reset token after use
        DB::table('otps')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        DB::table('otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'reset_token' => null,
                'reset_token_expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Send OTP via email (HTML format)
        $html = "<h2>Password Reset OTP</h2><p>Your OTP is: <strong>$otp</strong></p><p>This OTP is valid for 10 minutes.</p>";
        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to($request->email)
                ->subject('Your Password Reset OTP')
                ->html($html);
        });

        return response()->json(['message' => 'OTP sent to your email.']);
    }
} 