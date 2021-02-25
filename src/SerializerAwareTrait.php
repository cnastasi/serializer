<?php

declare(strict_types=1);

namespace CNastasi\Serializer;

use CNastasi\Serializer\Contract\ValueObjectSerializer;

trait SerializerAwareTrait
{
    protected ?ValueObjectSerializer $serializer = null;

    public function setSerializer(ValueObjectSerializer $serializer): void
    {
        $this->serializer = $serializer;
    }
}