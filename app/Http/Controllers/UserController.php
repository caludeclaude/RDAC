<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\UserRole;
use App\Http\Requests\StoreStaffAccountRequest;
use App\Mail\StaffAccountMail;
use App\Models\Office;
use App\Support\Audit;
use App\Support\TempPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::with('office')                         // one extra query for ALL offices
            ->when($search !== '', function ($query) use ($search) {
                // escape the characters that have a special meaning inside LIKE
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $query->where(function ($q) use ($like) {
                    $q->where('full_name', 'like', $like)
                      ->orWhere('username', 'like', $like)
                      ->orWhere('email', 'like', $like)
                      ->orWhere('role', 'like', $like)
                      ->orWhereHas('office', fn ($o) => $o->where('name', 'like', $like));
                });
            })
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();                              // page links keep the search text

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'total' => User::count(),
        ]);
    }

        public function create()
    {
        return view('users.create', ['provinces' => Office::PROVINCES]);
    }

    public function store(StoreStaffAccountRequest $request)
    {
        $data = $request->validated();
        $role = UserRole::from($data['role']);
        $officeName = $request->officeName();

        // Does this office already exist? (MySQL ignores upper/lower case here)
        $office = Office::where('name', $officeName)->first();

        if ($office && $office->office_type !== $role->officeType()) {
            throw ValidationException::withMessages([
                'role' => 'The office "' . $office->name . '" already exists as a different type of office.',
            ]);
        }

        $plain = TempPassword::generate();

        // Office + user are saved together: both succeed or neither does
        $user = DB::transaction(function () use ($data, $role, $office, $officeName, $plain, $request) {
            $office ??= Office::create([
                'name' => $officeName,
                'office_type' => $role->officeType(),
                'province' => $role === UserRole::Penro ? $data['province'] : null,
                'city' => $role === UserRole::Cenro ? $request->cleanCity() : null,
            ]);

            return User::create([
                'full_name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role' => $role,
                'office_id' => $office->id,
                'password' => $plain,              // scrambled by the model
                'must_change_password' => true,
                'is_active' => true,
            ]);
        });

        Audit::record("Created office staff account: {$user->username} ({$role->label()} - {$user->office->name})", $request->user());

        // The temporary password is never shown on screen, only e-mailed
        try {
            Mail::to($user->email)->send(new StaffAccountMail(
                $user->full_name, $role->label(), $user->office->name, $user->username, $user->email, $plain
            ));
        } catch (Throwable $e) {
            Audit::record('Staff account e-mail could not be sent: ' . $e->getMessage(), $request->user());

            return redirect()->route('users.index')->with('warning',
                "Staff account ({$role->label()}) created, but the e-mail with the temporary password could not be sent to {$user->email}. "
                . "Ask the user to use 'Forgot password?' on the login page to receive a new temporary password."
            );
        }

        return redirect()->route('users.index')->with('status',
            "Staff account ({$role->label()}) created successfully! Temporary credentials were e-mailed to {$user->email}."
        );
    }

        public function archive(Request $request, User $user)
    {
        $me = $request->user();

        if ($user->id === $me->id) {
            return back()->with('warning', 'You cannot archive your own account.');
        }

        if (! $user->is_active) {
            return back()->with('warning', 'That account is already archived.');
        }

        // Lock the active Super Admins while we count, so two admins cannot
        // archive each other at the same moment and leave nobody in charge
        $refusal = DB::transaction(function () use ($user) {
            if ($user->isSuperAdmin()) {
                $activeAdmins = User::where('role', UserRole::SuperAdmin->value)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->get();

                if ($activeAdmins->count() <= 1) {
                    return 'At least one active Super Admin account must remain.';
                }
            }

            $user->update(['is_active' => false]);

            return null;
        });

        if ($refusal) {
            return back()->with('warning', $refusal);
        }

        // Log the person out of every device right now, and drop any unused temporary password
        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->temporaryPasswords()->whereNull('used_at')->delete();

        Audit::record("Archived user: {$user->username}", $me);

        return back()->with('status', 'User account archived successfully.');
    }

    public function restore(Request $request, User $user)
    {
        if ($user->is_active) {
            return back()->with('warning', 'That account is already active.');
        }

        $user->update(['is_active' => true]);

        Audit::record("Unarchived user: {$user->username}", $request->user());

        return back()->with('status', 'User account restored successfully. They can log in again.');
    }
}