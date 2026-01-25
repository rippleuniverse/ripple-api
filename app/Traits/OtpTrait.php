<?php

namespace App\Traits;

use App\Mail\Auth\OtpMail;
use App\Models\OneTimePassword;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

trait OtpTrait
{

    private function generateSendOtp(User $user, string $type): void
    {
        $otp = $user->otps()->create([
            'type' => $type,
        ]);
        Mail::to($user->email)->send(new OtpMail($otp));
    }

    private function validateOtp(?OneTimePassword $otp): bool
    {
        if (!$otp) return false;

        error_log(now()->toString());
        error_log($otp->expires_at->toString());

        if (now()->gt($otp->expires_at)) {
            $otp->delete();
            return false;
        }

        return true;
    }

}
