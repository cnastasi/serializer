<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\Serializer\Contract\ValueObject;

/**
 * @implements ValueObjectConverter<ValueObject>
 */
class IdentityConverter implements ValueObjectConverter
{
    public function serialize($object)
    {
        return $object;
    }

    public function hydrate(string $targetClass, $value)
    {
        return $value;
    }

    public function accept($object): bool
    {
        return true;
    }
}