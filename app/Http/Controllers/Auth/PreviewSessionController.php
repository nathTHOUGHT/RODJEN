<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PreviewSessionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(config('preview.enabled'), 404);

        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $configuredUsername = (string) config('preview.username');
        $configuredPassword = (string) config('preview.password');

        $usernameMatches = $configuredUsername !== ''
            && hash_equals($configuredUsername, $credentials['username']);
        $passwordMatches = $configuredPassword !== ''
            && hash_equals($configuredPassword, $credentials['password']);

        if (! $usernameMatches || ! $passwordMatches) {
            throw ValidationException::withMessages([
                'username' => __('The provided credentials are invalid.'),
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('preview.user', [
            'username' => $configuredUsername,
            'name' => 'Admin',
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('preview.user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
