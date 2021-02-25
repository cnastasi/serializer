<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\Contract\ValueObject;
use CNastasi\DDD\Contract\Collection;

/**
 * @template T of null|int|string|bool|ValueObject|Collection
 *
 * @implements ValueObjectConverter<T, T>
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