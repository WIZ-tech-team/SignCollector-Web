<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64Image implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if it's a base64 string
        if (!preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            $fail('The :attribute must be a valid base64 image.');
        }

        // Check mime type
        $type = strtolower($type[1]);
        if (!in_array($type, ['jpeg', 'png', 'jpg', 'gif', 'svg'])) {
            $fail('The :attribute must be a valid image type (jpeg, png, jpg, gif, svg).');
        }

        // Decode and validate the image
        $image = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $value));
        if ($image === false) {
            $fail('The :attribute is not a valid base64 image.');
        }

    }
}
