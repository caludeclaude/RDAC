<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotCommonPassword implements ValidationRule
{
    private const COMMON = [
        'password', 'passw0rd', 'p@ssw0rd', 'p@ssword', 'qwerty', 'qwertyuiop', 'qwerty123',
        'letmein', 'welcome', 'admin', 'administrator', 'administrator1', 'iloveyou', 'abc123',
        'abcdefgh', 'abcdefghij', 'monkey', 'dragon', 'football', 'baseball', 'login', 'master',
        'shadow', 'sunshine', 'princess', 'changeme', 'denr', 'denr4a', 'denrcalabarzon',
        'calabarzon', 'rdac', 'rdacportal', 'philippines', 'pilipinas', 'manila', 'laguna',
        'calamba', 'mayapa', 'secret', 'trustno', 'whatever', 'freedom', 'internet',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $p = (string) $value;
        $lower = mb_strtolower($p);

        // "Password123!", "P@ssw0rd2026!" and "pa55word" all reduce to "password"
        $base = preg_replace('/[^a-z]+$/', '', $lower);   // drop trailing digits and symbols
        $deleet = fn (string $s) => strtr($s, ['@' => 'a', '$' => 's', '5' => 's', '0' => 'o', '1' => 'i', '3' => 'e']);

        $candidates = [
            $lower,
            $base,
            $deleet($base),
            $deleet($lower),
            preg_replace('/[^a-z]/', '', $lower),
            preg_replace('/[^a-z]/', '', $deleet($base)),
        ];

        if (array_intersect($candidates, self::COMMON)) {
            $fail('That password is too common. Please choose something harder to guess.');
            return;
        }

        if (preg_match('/^(.)\1+$/u', $p) || preg_match('/^(?:0123456789|1234567890|123456789)/', $p)) {
            $fail('That password is too easy to guess.');
        }
    }
}