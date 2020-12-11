<?php

namespace CNastasi\Serializer\Contract;

use CNastasi\Serializer\SerializerOptions;

interface ValueObjectSerializer
{
    /**
     * @param mixed $object
     * @param bool $isRoot
     *
     * @return mixed
     */
    public function serialize($object, bool $isRoot = true);

    /**
     * @param class-string $targetClass
     * @param mixed $value
     * @param bool $isRoot
     *
     * @return mixed
     */
    public function hydrate(string $targetClass, $value, bool $isRoot = true);

    public function getOptions(): SerializerOptions;
}