<?php
declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\Serializer\Contract\SimpleValueObject;
use CNastasi\Serializer\Contract\ValueObject;
use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;
use DateTimeImmutable;
use DateTimeInterface;

class DateTimeImmutableConverter implements ValueObjectConverter
{
    /**
     * @param DateTimeImmutable $object
     * @return string
     */
    public function serialize($object): string
    {
        if (!$this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        return $object->format(DateTimeInterface::RFC3339);
    }

    public function hydrate(string $targetClass, $value): DateTimeImmutable
    {
        if (!$this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        return $value instanceof DateTimeImmutable
            ? $value
            : DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, (string) $value);
    }

    /**
     * @param mixed $object
     * @return bool
     */
    public function accept($object): bool
    {
        return is_a($object, DateTimeImmutable::class, true);
    }
}
