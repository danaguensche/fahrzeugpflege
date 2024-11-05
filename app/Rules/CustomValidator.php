<?php

namespace App\Rules;
use Illuminate\Validation\Validator;

class CustomValidator extends Validator
{
    protected function replaceAttributePlaceholder($message, $attribute)
    {
        $attribute = str_replace(['"', '[', ']'], '', $attribute);
        return str_replace(':attribute', $attribute, $message);
    }
}
