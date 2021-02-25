<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\ValueObject\Primitive\DateTime;
use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Class DateTimeConverter
 * @package CNastasi\Serializer\Converter
 *
 * @implements ValueObjectConverter<DateTime, string>
 */
class DateTimeConverter implements ValueObjectConverter
{
    private string $format;

    public function __construct(string $format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    /**
     * @param DateTime $object
     *
     * @return string
     */
    public function serialize($object)
    {
        if (! $this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        return $object->format($this->format);
    }

    public function accept($object): bool
    {
        return \is_a($object, DateTime::class, true);
    }

    public function hydrate(string $targetClass, $value): DateTime
    {
        if (! $this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        return DateTime::fromDateTimeInterface(DateTimeImmutable::createFromFormat($this->format, $value));
    }
}
