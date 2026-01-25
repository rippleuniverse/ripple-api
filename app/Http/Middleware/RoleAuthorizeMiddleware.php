<?php

namespace App\Http\Middleware;

use App\Enums\Enums\StatusCode;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;

class RoleAuthorizeMiddleware
{
    use HttpResponses;

    public function handle(Request $request, Closure $next, ...$guards)
    {
        $role = $guards[0];
        $user = $request->user();

        if ($user->role !== $role) {
            return $this->failed(null, StatusCode::Forbidden->value, 'Unauthorized.');
        }

        return $next($request);
    }
}
