<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Normalizer;

/**
 * @template T The original object
 * @template I the input data
 * @template O the output data
 *
 */
interface Normalizer
{
    /**
     * @psalm-param T $object The origin object, before the serialization. Could be also a primitive
     * @param I $data The serialized data
     *
     * @return O the normalized data
     */
    public function normalize($object, $data);

    /**
     * @param class-string|object $object
     *
     * @return bool
     */
    public function accept($object): bool;
}