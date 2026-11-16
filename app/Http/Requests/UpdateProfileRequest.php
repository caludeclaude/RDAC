<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\PhMobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    // Anyone who reaches this route is already logged in, so we allow it
    public function authorize(): bool
    {
        return true;
    }

    // Runs BEFORE the rules: tidy up what was typed
    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => trim((string) $this->input('full_name')),
            'username' => trim((string) $this->input('username')),
            'phone' => preg_replace('/[\s\-]+/', '', (string) $this->input('phone')),   // remove spaces and dashes
        ]);
    }

    public function rules(): array
    {
        $me = $this->user();

        return [
            'full_name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'regex:/^[A-Za-z0-9][A-Za-z0-9._@+\-]{2,63}$/',
                Rule::unique('users', 'username')->ignore($me->id),   // ignore myself
                // Cannot be the same as somebody else's e-mail either
                function ($attribute, $value, $fail) use ($me) {
                    $clash = User::where('email', $value)->where('id', '!=', $me->id)->exists();
                    if ($clash) {
                        $fail('The chosen username is already in use.');
                    }
                },
            ],
            'phone' => ['nullable', new PhMobileNumber],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username must be 3-64 characters: letters, numbers and . _ @ + - only, starting with a letter or number.',
            'username.unique' => 'The chosen username is already in use.',
        ];
    }
}