<?php

namespace CNastasi\Example;

use CNastasi\DDD\Contract\SimpleValueObject;

/**
 * Class Phone
 * @package CNastasi\Example
 *
 * @implements SimpleValueObject<string>
 */
class Phone implements SimpleValueObject
{
    private string $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }
}