<?php

namespace CNastasi\Serializer;


class SerializerOptionsDefault implements SerializerOptions
{

    private bool $ignoreNull;

    /**
     * SerializerOptions constructor.
     * @param bool $ignoreNull if true exclude property from serialization when the value is null
     */
    public function __construct(bool $ignoreNull)
    {
        $this->ignoreNull = $ignoreNull;
    }

    public function isIgnoreNull(): bool
    {
        return $this->ignoreNull;
    }
}