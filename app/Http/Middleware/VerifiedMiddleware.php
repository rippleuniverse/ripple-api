<?php

namespace App\Http\Middleware;

use App\Enums\Enums\StatusCode;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;

class VerifiedMiddleware
{
    use HttpResponses;

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user?->email_verified_at) {
            return $this->failed(null, StatusCode::Unauthorized->value, 'Email not verified.');
        }

        return $next($request);
    }
}
