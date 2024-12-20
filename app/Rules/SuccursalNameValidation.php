<?php

namespace App\Rules;

use App\Models\Company;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SuccursalNameValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected string $id;
    protected string $name;
    public function __construct(string $id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $company = Company::find($this->id);
        if ($company->succursale()->where('name', $this->name)->exists()) {
            $fail('Cette succursale existe déjà');
        }
    }
}
