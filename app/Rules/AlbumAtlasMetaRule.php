<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class AlbumAtlasMetaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check for empty array
        if (empty($value)) {
            $fail(__('validation.required', ['attribute' => $attribute]));
        }

        if (is_array($value)) {
            // Check array keys
            foreach ($value as $key => $item) {
                if ( ! in_array($key, ['access', 'shared_for'])) {
                    $fail(__('validation.exists', ['attribute' => $attribute]));
                }
            }
        }
    }
}
