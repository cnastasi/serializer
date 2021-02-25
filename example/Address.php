<?php

namespace CNastasi\Example;

use CNastasi\DDD\Contract\Comparable;
use CNastasi\DDD\Contract\CompositeValueObject;
use CNastasi\DDD\Error\IncomparableObjects;

class Address implements CompositeValueObject
{
    private string $street;

    private string $city;

    public function __construct(string $street, string $city)
    {
        $this->street = $street;
        $this->city = $city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function equalsTo(Comparable $item): bool
    {
        if ($item instanceof static) {
            return $this->street === $item->street
                && $this->city === $item->city;
        }

        throw new IncomparableObjects($item, $this);
    }
}