<?php

namespace App\Http\Requests;

use App\Models\Office;
use App\Rules\PhMobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    // Every logged-in role may file a complaint
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $clean = fn ($v) => preg_replace('/\s+/', ' ', trim((string) $v));

        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'complainant_name' => $clean($this->input('complainant_name')),
            'complainant_email' => Str::lower(trim((string) $this->input('complainant_email'))),
            'complainant_phone' => preg_replace('/[\s\-]+/', '', (string) $this->input('complainant_phone')),
            'subject' => $clean($this->input('subject')),
            'barangay' => $clean($this->input('barangay')),
            'city' => $clean($this->input('city')),
            'description' => trim((string) $this->input('description')),
        ]);
    }

    public function rules(): array
    {
        return [
            'is_anonymous' => ['boolean'],

            // The name is only needed when the complaint is NOT anonymous
            'complainant_name' => $this->boolean('is_anonymous')
                ? ['nullable']
                : ['required', 'string', 'max:100'],
            'complainant_email' => ['nullable', 'email', 'max:100'],
            'complainant_phone' => ['nullable', new PhMobileNumber],

            'subject' => ['required', 'string', 'max:150'],

            'target_office_id' => [
                'required', 'integer',
                function ($attribute, $value, $fail) {
                    if (! Office::routable()->whereKey($value)->exists()) {
                        $fail('The selected office is not a registered office.');
                    }
                },
            ],

            'complaint_type_id' => [
                'required',
                Rule::exists('complaint_types', 'id')->where('is_active', true),
            ],

            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', Rule::in(Office::PROVINCES)],

            'description' => ['required', 'string', 'max:5000'],

            'attachment' => ['nullable', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:10240'],

            'privacy_consent' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'complainant_name' => 'complainant name',
            'complainant_email' => 'complainant e-mail',
            'complainant_phone' => 'complainant mobile number',
            'target_office_id' => 'office',
            'complaint_type_id' => 'nature of complaint',
            'city' => 'city / municipality',
        ];
    }

    public function messages(): array
    {
        return [
            'privacy_consent.accepted' => 'Please read and acknowledge the Privacy Notice before submitting.',
            'description.max' => 'The description is too long (maximum 5000 characters).',
            'attachment.mimetypes' => 'Only PDF, JPG or PNG attachments are allowed.',
            'attachment.max' => 'The attachment is larger than 10 MB.',
            'attachment.uploaded' => 'The attachment could not be uploaded. Please choose a smaller file and try again.',
        ];
    }
}