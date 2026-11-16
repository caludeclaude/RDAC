<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\NotCommonPassword;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('password.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 'string', 'max:128',
                Password::min(12)->letters()->numbers(),
                new NotCommonPassword,
                'different:current_password',
                'confirmed',
            ],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.different' => 'The new password must be different from the current password.',
            'password.confirmed' => 'The two new passwords do not match.',
        ]);

        $user = $request->user();

        // The model scrambles the new password automatically (the 'hashed' cast)
        $user->update([
            'password' => $request->input('password'),
            'must_change_password' => false,
        ]);

        // End every OTHER session of this account (a stolen session stops working)
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        $request->session()->regenerate();

        Audit::record('Changed password; all other sessions were ended.', $user);

        return redirect()->route('dashboard')->with('status', 'Password updated successfully. Other devices were logged out.');
    }
}