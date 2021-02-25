<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Exception;

class LoopGuardNotInjectedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Mismatch configuration: Loop Guard not injected');
    }
}