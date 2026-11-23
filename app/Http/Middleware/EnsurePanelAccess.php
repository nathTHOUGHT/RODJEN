<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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

        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->guest(route('verification.notice'));
        }

        return $next($request);
    }
}
