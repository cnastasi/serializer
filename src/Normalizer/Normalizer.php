<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Normalizer;

/**
 * @template T
 */
interface Normalizer
{
    /**
     * @phpstan-param T $object
     * @param mixed $object The origin object, before the serialization. Could be also a primitive
     * @param int|string|array<mixed> $data The serialized data
     *
     * @return int|string|array<mixed>
     */
    public function normalize($object, $data);

    /**
     * @param object|string $object
     *
     * @return bool
     */
    public function accept($object): bool;
}