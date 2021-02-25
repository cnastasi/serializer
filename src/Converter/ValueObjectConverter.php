<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\Contract\Collection;
use CNastasi\DDD\Contract\ValueObject;

/**
 * @template I
 * @template O
 */
interface ValueObjectConverter
{
    /**
     * @param I $object
     *
     * @return O
     */
    public function serialize($object);

    /**
     * @param class-string $targetClass
     * @param O $value
     *
     * @return I
     */
    public function hydrate(string $targetClass, $value);

    /**
     * @param I|object|class-string $object
     *
     * @return bool
     */
    public function accept($object): bool;
}