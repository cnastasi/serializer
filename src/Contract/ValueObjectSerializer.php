<?php

namespace CNastasi\Serializer\Contract;

use CNastasi\DDD\Contract\Collection;
use CNastasi\DDD\Contract\ValueObject;
use CNastasi\Serializer\SerializerOptions;

/**
 * @template I
 * @template O
 */
interface ValueObjectSerializer
{
    /**
     * @param I $object
     * @param bool $isRoot
     *
     * @return O
     */
    public function serialize($object, bool $isRoot = true);

    /**
     * @param class-string $targetClass
     * @param O $value
     * @param bool $isRoot
     *
     * @return I
     */
    public function hydrate(string $targetClass, $value, bool $isRoot = true);

    public function getOptions(): SerializerOptions;
}