<?php

namespace App\Support;

final class Emoji
{
    /**
     * Unicode ranges for pictographs, emoticons, flags, keycaps, skin tones,
     * variation selectors, and zero-width joiners used by emoji sequences.
     */
    public const PATTERN = '/[\x{1F000}-\x{1FAFF}\x{1F1E6}-\x{1F1FF}\x{1F3FB}-\x{1F3FF}\x{2600}-\x{27BF}\x{2300}-\x{23FF}\x{25A0}-\x{25FF}\x{2B00}-\x{2BFF}\x{FE0F}\x{200D}\x{20E3}\x{3030}\x{303D}\x{3297}\x{3299}]/u';

    public static function contains(?string $value): bool
    {
        return is_string($value) && preg_match(self::PATTERN, $value) === 1;
    }

    public static function strip(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $cleaned = preg_replace(self::PATTERN, '', $value) ?? $value;
        $cleaned = preg_replace('/[ \t]{2,}/u', ' ', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\s+([,.!?;:])/u', '$1', $cleaned) ?? $cleaned;

        return trim($cleaned);
    }
}
