<?php

namespace App\Services;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public const MAX_LOGIN_ATTEMPTS = 5;

    public const LOCKOUT_MINUTES = 15;

    public function attempt(string $email, string $password, bool $remember = false): array
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return ['success' => false, 'reason' => 'invalid_credentials'];
        }

        if (! $user->is_active) {
            return ['success' => false, 'reason' => 'inactive'];
        }

        if ($user->isLocked()) {
            return ['success' => false, 'reason' => 'locked'];
        }

        if (! Hash::check($password, $user->password)) {
            $attempts = (int) $user->login_attempts + 1;
            $updates = ['login_attempts' => $attempts];

            if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
                $updates['locked_until'] = now()->addMinutes(self::LOCKOUT_MINUTES);
            }

            $user->update($updates);

            return ['success' => false, 'reason' => $updates['locked_until'] ?? null ? 'locked' : 'invalid_credentials'];
        }

        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ]);

        auth()->login($user, $remember);
        AuditLogger::auth('login', $user->id);

        return ['success' => true, 'user' => $user];
    }

    public function logout(): void
    {
        if (auth()->check()) {
            AuditLogger::auth('logout', auth()->id());
        }

        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}