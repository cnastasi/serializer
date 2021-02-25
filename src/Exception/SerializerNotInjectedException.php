<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Exception;

class SerializerNotInjectedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Mismatch configuration: Serializer not injected');
    }
}