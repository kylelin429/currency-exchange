<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Currency implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->isSymbolExistButNotStartInValue($value)) {
            return false;
        }

        if (!$this->isValidNumber($value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid currency.';
    }

    private function isSymbolExistButNotStartInValue($value)
    {
        $symbol = '$';

        if (strpos($value, $symbol) !== false) {
            if (substr($value, 0, strlen($symbol)) !== $symbol) {
                return true;
            }
        }

        return false;
    }

    private function isValidNumber($value)
    {
        $value = str_replace('$', "", $value);
        $value = str_replace(',', "", $value);
        return is_numeric($value);
    }
}
