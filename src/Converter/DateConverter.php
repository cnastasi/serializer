<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\ValueObject\Primitive\Date;
use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;

class DateConverter implements ValueObjectConverter
{
    public function serialize($object)
    {
        if (! $this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        return $object->serialize();
    }

    public function accept($object): bool
    {
        return \is_a($object, Date::class, true);
    }

    public function hydrate(string $targetClass, $value): Date
    {
        if (! $this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        return Date::fromString($value);
    }
}
