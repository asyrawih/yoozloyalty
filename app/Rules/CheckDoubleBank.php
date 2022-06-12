<?php

namespace App\Rules;

use App\Models\Bank;
use Illuminate\Contracts\Validation\Rule;

class CheckDoubleBank implements Rule
{

    protected string $account_type;

    /**
     * The ID that should be ignored.
     *
     * @var mixed
     */
    protected $ignore;

    /**
     * The name of the ID column.
     *
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $account_type, $id)
    {
        $this->account_type = $account_type;
        $this->ignore = $id;
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
        $bank = Bank::query()
            ->where('account_number', $value)
            ->where('account_type', $this->account_type)
            ->first();

        if ($bank) {
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
        return "Account number already exist for account type {$this->account_type}";
    }
}
