<?php
declare(strict_types=1);

namespace CNastasi\Serializer\Exception;

use RuntimeException;

class UnableToHydrateException extends RuntimeException
{
    public function __construct(string $targetClass)
    {
        parent::__construct('Hydrator was not able to hydrate ' . $targetClass);
    }
}