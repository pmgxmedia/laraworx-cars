<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(protected AuthService $auth)
    {
    }

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $result = $this->auth->attempt($credentials['email'], $credentials['password'], $request->boolean('remember'));

        if (! $result['success']) {
            return match ($result['reason']) {
                'locked' => back()->withErrors(['email' => 'Too many failed attempts. Your account is temporarily locked. Try again later.'])->onlyInput('email'),
                'inactive' => back()->withErrors(['email' => 'This account has been deactivated.'])->onlyInput('email'),
                'invalid_credentials' => back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email'),
                default => back()->withErrors(['email' => 'Unable to sign in.'])->onlyInput('email'),
            };
        }

        $request->session()->regenerate();

        $user = $result['user'];

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isDealership() || $user->isPrivateSeller()) {
            return redirect()->intended(route('dealer.dashboard'));
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->auth->logout();

        return redirect('/');
    }
}