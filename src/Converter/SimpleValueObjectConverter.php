<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;
use CNastasi\Serializer\Contract\SimpleValueObject;
use CNastasi\Serializer\Contract\ValueObject;

/**
 * Class SimpleValueObjectConverter
 * @package CNastasi\Serializer\Converter
 *
 * @template T of SimpleValueObject<mixed>
 * @implements ValueObjectConverter<T>
 */
class SimpleValueObjectConverter implements ValueObjectConverter
{
    /**
     * @phpstan-param T $object
     * @param object $object
     *
     * @return mixed
     */
    public function serialize($object)
    {
        if (!$this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        return $object->value();
    }

    public function accept($object): bool
    {
        return is_a($object, SimpleValueObject::class, true);
    }

    /**
     * @param class-string $targetClass
     * @param mixed $value
     *
     * @return ValueObject
     * @phpstan-return T
     *
     * @throws UnacceptableTargetClassException
     */
    public function hydrate(string $targetClass, $value): ValueObject
    {
        if (!$this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        return new $targetClass($value);
    }
}