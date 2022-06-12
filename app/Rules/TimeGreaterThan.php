<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TimeGreaterThan implements Rule
{
    private $time_from;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($time_from)
    {
        $this->time_from = $time_from;
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
        return $value > $this->time_from;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Time to should be greater than time from.';
    }
}
