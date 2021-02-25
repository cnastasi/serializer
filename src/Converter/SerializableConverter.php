<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\Contract\Serializable;

/**
 * @implements ValueObjectConverter<Serializable, mixed>
 */
class SerializableConverter implements ValueObjectConverter
{
    public function serialize($object)
    {
        return $object->serialize();
    }

    public function hydrate(string $targetClass, $value)
    {
        return $value;
    }

    public function accept($object): bool
    {
        return $object instanceof Serializable;
    }
}