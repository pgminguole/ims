<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFileExtension implements ValidationRule
{
    protected $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xlsx', 'xls'];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Extract the file extension by splitting the string at the last dot
        $extension = strtolower(substr(strrchr($value, '.'), 1));

        // Check if the extension is in the allowed list
        if (!in_array($extension, $this->allowedExtensions)) {
            $fail('The :attribute is not a valid file extension.');
        }
    }

}
