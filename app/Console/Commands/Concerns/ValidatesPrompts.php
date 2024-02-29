<?php

namespace App\Console\Commands\Concerns;

trait ValidatesPrompts
{
    /**
     * Validate the given attribute.
     *
     * @param string $attribute
     * @param mixed $value
     * @param mixed $rules
     *
     * @return void
     */
    public function validate(string $attribute, $value, $rules)
    {
        $validator = validator([$attribute => $value], [$attribute => $rules]);

        if ($validator->fails())
        {
            return $validator->errors()->first($attribute);
        }
    }
}
