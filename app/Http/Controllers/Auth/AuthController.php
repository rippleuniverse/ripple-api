<?php

namespace App\Http\Controllers\Auth;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\ProfileResource;
use App\Mail\Auth\WelcomeMail;
use App\Mail\Newsletter\SubscribedMail;
use App\Models\Coupon;
use App\Models\NewsletterSubscription;
use App\Models\User;
use App\Traits\OtpTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    use OtpTrait;

    public function signUp(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['string', 'required', 'max:255'],
            'email' => ['string', 'required', 'email', 'unique:users,email'],
            'password' => ['string', 'required', 'confirmed', 'min:8'],
            'can_subscribe_newsletter' => ['boolean', 'required'],
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $userInitials = substr($user->full_name, 0, 3);
        $code = strtoupper($userInitials . substr(md5(uniqid()), 0, 5));

        if (Coupon::where('code', $code)->exists()) {
            $code = strtoupper($userInitials . substr(md5(uniqid()), 0, 5)) . $user->id;
        }

        Coupon::create([
            'user_id' => $user->id,
            'is_active' => true,
            'code' => $code,
            'percentage_value' => 10,
            'fixed_value' => json_encode([]),
            'type' => 'percentage',
            'is_created_by_admin' => false,
        ]);


        $this->generateSendOtp($user, 'email_verification');
        Mail::to($user->email)->send(new WelcomeMail($user));

        if ($data['can_subscribe_newsletter']) {
            NewsletterSubscription::create(['email' => $data['email']]);
            Mail::to($user->email)->send(new SubscribedMail());
        }


        $token = $user->createToken('auth_token')->plainTextToken;
        $userData = [
            'token' => $token,
            'user' => new ProfileResource($user)
        ];
        return $this->success($userData, 'Signed up successfully.');
    }

    public function signIn(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $signedIn = auth()->attempt($data);

        if (!$signedIn) {
            return $this->failed(null, StatusCode::Unauthorized->value, 'Invalid credentials.');
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;
        $userData = [
            'token' => $token,
            'user' => new ProfileResource($user)
        ];
        return $this->success($userData, 'Signed in successfully.');
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $profile = new ProfileResource($user);
        return $this->success($profile, 'User profile');
    }

    public function resendEmailVerification(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return $this->failed(null, StatusCode::BadRequest->value, 'Email already verified.');
        }

        $this->generateSendOtp($user, 'email_verification');

        return $this->success(null, 'Verification email sent successfully.');
    }

    public function verifyEmail(Request $request)
    {
        $data = $request->validate([
            'otp' => ['required', 'string', 'max:4', 'min:4'],
        ]);
        $user = $request->user();
        $otp = $user->otps()->where('code', $data['otp'])
            ->where('type', 'email_verification')
            ->first();

        $isOtpValid = $this->validateOtp($otp);

        if (!$isOtpValid) {
            return $this->failed(null, StatusCode::BadRequest->value, 'OTP is invalid or has expired.');
        }

        $user->update(['email_verified_at' => now()]);
        $otp->delete();
        return $this->success(null, 'Email verified successfully.');
    }

    public function sendPasswordReset(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email']
        ]);
        $user = User::where('email', $data['email'])->first();
        $this->generateSendOtp($user, 'password_reset');
        return $this->success(null, 'Password reset code sent successfully.');
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'otp' => ['required', 'string', 'max:4', 'min:4'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'email' => ['required', 'email', 'exists:users,email']
        ]);

        $user = User::where('email', $data['email'])->first();
        $otp = $user->otps()->where('code', $data['otp'])
            ->where('type', 'password_reset')
            ->first();

        $isOtpValid = $this->validateOtp($otp);

        if (!$isOtpValid) {
            return $this->failed(null, StatusCode::BadRequest->value, 'OTP is invalid or has expired.');
        }

        $user->update(['password' => Hash::make($data['password'])]);
        $otp->delete();
        return $this->success(null, 'Password reset successfully.');

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully.');
    }
}
