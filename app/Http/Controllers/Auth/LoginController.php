<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;     // wrong passwords allowed...
    private const LOCK_SECONDS = 900;   // ...inside this window (15 minutes)

    public function show()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        // 1. Is whatever was typed already locked?
        $typed = Str::lower(trim($data['login']));
        $inputKey = 'login:' . $typed;
        $this->failIfLocked($inputKey);

        // 2. Find the person by username or email
        $user = User::where('username', $data['login'])
            ->orWhere('email', $data['login'])
            ->first();

        $keys = [$inputKey];
        if ($user) {
            $userKey = 'login:' . Str::lower($user->username);
            if ($userKey !== $inputKey) {
                $keys[] = $userKey;
            }
            $this->failIfLocked($userKey);
        }

        // 3. Check the password
        $passwordOk = $user && Hash::check($data['password'], $user->password);

        // NEW: not the normal password? It may be a temporary one from "Forgot password"
        $viaTemp = null;
        if ($user && ! $passwordOk) {
            $viaTemp = $user->temporaryPasswords()
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->get()
                ->first(fn ($t) => Hash::check($data['password'], $t->password_hash));

            $passwordOk = (bool) $viaTemp;
        }

        if (! $passwordOk) {
            foreach ($keys as $key) {
                RateLimiter::hit($key, self::LOCK_SECONDS);

                if (RateLimiter::attempts($key) === self::MAX_ATTEMPTS) {
                    Audit::record(
                        "LOCKOUT (login): '" . Str::after($key, 'login:') . "' is locked for 15 minutes after "
                        . self::MAX_ATTEMPTS . ' failed attempts.'
                    );
                }
            }

            Audit::record('Failed login attempt.', $user, $typed);

            throw ValidationException::withMessages([
                'login' => 'Incorrect username/email or password.',
            ]);
        }

        // 4. Correct password, but the account was archived
        if (! $user->is_active) {
            Audit::record('Login refused: account is archived or inactive.', $user);

            throw ValidationException::withMessages([
                'login' => 'Account is archived or inactive.',
            ]);
        }

        // 5. Success
        foreach ($keys as $key) {
            RateLimiter::clear($key);
        }

        // NEW: a temporary password becomes the real password until a new one is chosen
        if ($viaTemp) {
            $user->update(['password' => $data['password'], 'must_change_password' => true]);
            $viaTemp->update(['used_at' => now()]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        Audit::record($viaTemp ? 'Staff user authenticated with a temporary password.' : 'Staff user authenticated.', $user);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Audit::record('Logged out.', $request->user());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function failIfLocked(string $key): void
    {
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            throw ValidationException::withMessages([
                'login' => "Too many failed attempts. Please try again in {$minutes} minute(s).",
            ]);
        }
    }
}