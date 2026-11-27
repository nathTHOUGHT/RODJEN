<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        if (
            config('preview.enabled')
            && app()->environment(['local', 'testing'])
            && $request->session()->has('preview.user')
        ) {
            return $next($request);
        }

        $userType = UserType::tryFrom((string) $request->user()?->user_type);

        abort_unless($userType && in_array($userType->value, $types, true), 403);

        return $next($request);
    }
}
