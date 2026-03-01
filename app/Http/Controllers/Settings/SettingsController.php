<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\ShippingFee;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function siteLogin(Request $request)
    {
        $data = $request->validate([
            'site_unlock_password' => ['required', 'string'],
        ]);

        $unlockPassword = Settings::first()?->site_unlock_password;

        $validated = Hash::check($data['site_unlock_password'], $unlockPassword);

        if (!$validated) {
            return $this->failed(null, StatusCode::Unauthorized->value, 'Invalid unlock password');
        }

        $payload = [
            'iss' => config('app.name'),          // Issuer
            'sub' => $request->ip(),                 // Subject (user id or whatever)
            'iat' => time(),              // Issued at
            'exp' => time() + (3600 * 24)        // Expiration time (1 day)
        ];
        $jwt = JWT::encode($payload, config('jwt.secret'), config('jwt.algo'));

        return $this->success([
            'token' => $jwt
        ], 'Site unlocked successfully');
    }

    public function checkSiteLockStatus(Request $request)
    {
        $token = $request->bearerToken();
        $secret = config('jwt.secret');

        try {
            JWT::decode($token, new Key($secret, config('jwt.algo')));
            return $this->success(['is_unlocked' => true], 'Site lock status retrieved successfully');
        } catch (\Exception $e) {
            return $this->failed(['is_unlocked' => false], StatusCode::Unauthorized->value, 'Site is locked');
        }

    }

    public function shippingFee()
    {
        $fees = ShippingFee::first();
        return $this->success([
            'fees' => json_decode($fees->fees, true)
        ]);
    }

}
