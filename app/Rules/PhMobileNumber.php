<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhMobileNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 09 followed by 9 more digits = 11 digits in total
        if (! preg_match('/^09\d{9}$/', (string) $value)) {
            $fail('Enter a mobile number in the format 09XXXXXXXXX (11 digits).');
        }
    }
}