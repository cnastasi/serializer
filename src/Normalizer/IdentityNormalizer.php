<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Normalizer;

use CNastasi\DDD\Contract\ValueObject;

/**
 * @implements Normalizer<ValueObject>
 */
class IdentityNormalizer implements Normalizer
{
    public function accept($object): bool
    {
        return true;
    }

    public function normalize($object, $data)
    {
        return $data;
    }
}