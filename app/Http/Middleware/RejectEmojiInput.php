<?php

namespace App\Http\Middleware;

use App\Support\Emoji;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RejectEmojiInput
{
    /**
     * Reject emoji in every string input sent to the admin area.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethodSafe()) {
            foreach ($request->all() as $field => $value) {
                if ($this->containsEmoji($value)) {
                    throw ValidationException::withMessages([
                        $field => 'Emoji tidak diperbolehkan.',
                    ]);
                }
            }
        }

        return $next($request);
    }

    private function containsEmoji(mixed $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->containsEmoji($item)) {
                    return true;
                }
            }

            return false;
        }

        return is_string($value) && Emoji::contains($value);
    }
}
