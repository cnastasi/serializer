<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\ValueObject\Primitive\DateTime;
use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;

class DateTimeConverter implements ValueObjectConverter
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
        return \is_a($object, DateTime::class, true);
    }

    public function hydrate(string $targetClass, $value): DateTime
    {
        if (! $this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        return DateTime::fromString($value);
    }
}
