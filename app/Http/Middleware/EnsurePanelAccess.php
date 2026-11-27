<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePanelAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (
            config('preview.enabled')
            && app()->environment(['local', 'testing'])
            && $request->session()->has('preview.user')
        ) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect()->guest(route('verification.notice'));
        }

        abort_unless(UserType::tryFrom((string) $user->user_type) !== null, 403);

        return $next($request);
    }
}
