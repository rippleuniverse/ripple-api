<?php

namespace App\Http\Middleware;

use App\Enums\StatusCode;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;

class StatusAuthorizeMiddleware
{
    use HttpResponses;

    public function handle(Request $request, Closure $next, ...$guards)
    {
        $status = $guards[0];
        $user = $request->user();

        if ($user->status !== $status) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }

        return $next($request);
    }
}
