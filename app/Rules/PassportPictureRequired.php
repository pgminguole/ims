<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PassportPictureRequired implements ValidationRule
{

    protected $pictureNames;
    protected $documentNames;

    public function __construct($pictureNames, $documentNames)
    {
        $this->pictureNames = $pictureNames;
        $this->documentNames = $documentNames;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (count($this->documentNames) != count($this->pictureNames)) {
            $fail('The :attribute is required.');
        }
    }


}
