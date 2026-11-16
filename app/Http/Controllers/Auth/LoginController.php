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

        // 1. Is whatever was typed already locked? (checked before touching the database)
        $typed = Str::lower(trim($data['login']));
        $inputKey = 'login:' . $typed;
        $this->failIfLocked($inputKey);

        // 2. Find the person by username or email
        $user = User::where('username', $data['login'])
            ->orWhere('email', $data['login'])
            ->first();

        // The attempts are counted for what was typed AND for the real username,
        // so switching between username and email does not give extra tries
        $keys = [$inputKey];
        if ($user) {
            $userKey = 'login:' . Str::lower($user->username);
            if ($userKey !== $inputKey) {
                $keys[] = $userKey;
            }
            $this->failIfLocked($userKey);
        }

        // 3. Wrong username or password
        if (! $user || ! Hash::check($data['password'], $user->password)) {
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

        Auth::login($user);
        $request->session()->regenerate();
        Audit::record('Staff user authenticated.', $user);

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

    // Stops the request with a message when this key has used up its attempts
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