<?php
declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnableToHydrateException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Class DateTimeImmutableConverter
 * @package CNastasi\Serializer\Converter
 *
 * @implements ValueObjectConverter<DateTimeImmutable>
 */
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

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        $result = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, (string) $value);

        if ($result === false) {
            throw new UnableToHydrateException($targetClass);
        }

        return $result;
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
