<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Normalizer;

use CNastasi\DDD\Contract\ValueObject;
use CNastasi\DDD\Contract\Collection;

/**
 * @template T
 *
 * @implements Normalizer<T, int|string|bool|array<mixed>|null, int|string|bool|array<mixed>|null>
 */
final class IdentityNormalizer implements Normalizer
{
    /**
     * @psalm-param class-string|object $object
     *
     * @psalm-return true
     */
    public function accept($object): bool
    {
        return true;
    }

    public function normalize($object, $data)
    {
        return $data;
    }
}