<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TemporaryPasswordMail;
use App\Models\User;
use App\Support\Audit;
use App\Support\TempPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:100'],
        ]);

        // The SAME reply is always shown, so nobody can discover which accounts exist
        $generic = 'If an active account matches that username or e-mail, a temporary password has been sent '
                 . 'to its registered e-mail address. It is valid for 30 minutes, and your current password '
                 . 'keeps working until you use it.';

        $identifier = Str::lower(trim($data['identifier']));

        // At most 3 requests per hour for each identifier
        $key = 'password-reset:' . $identifier;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('status', $generic);
        }
        RateLimiter::hit($key, 3600);

        $user = User::where('username', $identifier)->orWhere('email', $identifier)->first();

        if (! $user || ! $user->is_active) {
            return back()->with('status', $generic);
        }

        if (! $user->email) {
            Audit::record('Password reset requested but no e-mail is on file.', $user);
            return back()->with('status', $generic);
        }

        $plain = TempPassword::generate();

        // A newer request replaces the older pending ones
        $user->temporaryPasswords()->whereNull('used_at')->delete();
        $user->temporaryPasswords()->create([
            'password_hash' => Hash::make($plain),
            'expires_at' => now()->addMinutes(30),
        ]);

        Audit::record('Requested password reset.', $user);

        try {
            Mail::to($user->email)->send(new TemporaryPasswordMail($user->full_name, $user->username, $plain));
        } catch (Throwable $e) {
            // The e-mail did not go out, so the temporary password is useless: remove it
            $user->temporaryPasswords()->whereNull('used_at')->delete();
            Audit::record('Password reset e-mail could not be sent: ' . $e->getMessage(), $user);
        }

        return back()->with('status', $generic);
    }
}