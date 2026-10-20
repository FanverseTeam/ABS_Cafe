<?php

namespace App\Rules;

use App\Support\Emoji;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoEmoji implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Emoji::contains(is_string($value) ? $value : null)) {
            $fail('Emoji tidak diperbolehkan.');
        }
    }
}
