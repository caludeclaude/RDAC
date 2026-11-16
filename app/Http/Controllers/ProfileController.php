<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Support\Audit;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()->load('office')]);
    }

    // Because we ask for UpdateProfileRequest, Laravel validates BEFORE this method runs
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $oldUsername = $user->username;

        $user->update([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'phone' => $data['phone'] ?: null,    // empty box = no phone
        ]);

        if ($oldUsername !== $user->username) {
            Audit::record("Changed username from '{$oldUsername}' to '{$user->username}'.", $user);
        }
        Audit::record('Updated profile settings.', $user);

        return redirect()->route('profile.edit')->with('status', 'Profile updated successfully.');
    }
}