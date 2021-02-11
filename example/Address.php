<?php

namespace CNastasi\Example;

use CNastasi\DDD\Contract\CompositeValueObject;

class Address implements CompositeValueObject
{
    private string $street;

    private string $city;

    public function __construct(string $street, string $city)
    {
        $this->street = $street;
        $this->city   = $city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}