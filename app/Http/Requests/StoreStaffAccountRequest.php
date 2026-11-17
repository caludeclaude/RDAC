<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Office;
use App\Models\User;
use App\Rules\PhMobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreStaffAccountRequest extends FormRequest
{
    // The role middleware already limits this page to Super Admins
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => trim((string) $this->input('full_name')),
            'username' => Str::lower(trim((string) $this->input('username'))),
            'email' => Str::lower(trim((string) $this->input('email'))),
            'phone' => preg_replace('/[\s\-]+/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'regex:/^[A-Za-z0-9][A-Za-z0-9._@+\-]{2,63}$/',
                'unique:users,username',
                // a username may not be the same as somebody's e-mail
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->exists()) {
                        $fail('Username is already taken.');
                    }
                },
            ],
            'email' => [
                'required', 'email', 'max:100',
                'unique:users,email',
                // an e-mail may not be the same as somebody's username
                function ($attribute, $value, $fail) {
                    if (User::where('username', $value)->exists()) {
                        $fail('Email is already registered.');
                    }
                },
            ],
            'phone' => ['required', new PhMobileNumber],
            'role' => ['required', Rule::enum(UserRole::class)],

            // Only the field that belongs to the chosen role is required
            'province' => ['required_if:role,penro', 'nullable', Rule::in(Office::PROVINCES)],
            'city' => ['required_if:role,cenro', 'nullable', 'string', 'max:90'],
            'division' => ['required_if:role,division', 'nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username must be 3-64 characters: letters, numbers and . _ @ + - only, starting with a letter or number.',
            'username.unique' => 'Username is already taken.',
            'email.unique' => 'Email is already registered.',
            'province.required_if' => 'Select the province.',
            'city.required_if' => 'Enter the city or municipality.',
            'division.required_if' => 'Enter the division name.',
        ];
    }

    // "CENRO Calamba" is built from the city; a typed "CENRO " prefix is not doubled
    public function cleanCity(): string
    {
        $city = preg_replace('/\s+/', ' ', trim((string) $this->input('city')));

        return trim(preg_replace('/^cenro\s+/i', '', $city));
    }

    // The office name that will be saved, built from the role and its office field
    public function officeName(): string
    {
        return match ($this->input('role')) {
            'super_admin' => 'Regional Office',
            'penro' => 'PENRO ' . $this->input('province'),
            'cenro' => 'CENRO ' . $this->cleanCity(),
            default => preg_replace('/\s+/', ' ', trim((string) $this->input('division'))),
        };
    }
}