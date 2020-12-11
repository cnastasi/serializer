<?php

declare(strict_types=1);

namespace CNastasi\Serializer;

use CNastasi\Serializer\Contract\LoopGuardAware;
use CNastasi\Serializer\Contract\SerializerAware;
use CNastasi\Serializer\Contract\ValueObject;
use CNastasi\Serializer\Contract\ValueObjectSerializer;
use CNastasi\Serializer\Converter\ValueObjectConverter;

/**
 * Class DefaultSerializer
 * @package CNastasi\Serializer
 *
 */
class DefaultSerializer implements ValueObjectSerializer
{
    /** @var ValueObjectConverter<ValueObject>[] $converters */
    private array $converters;

    private SerializationLoopGuard $loopGuard;

    private SerializerOptionsDefault $options;

    /**
     * @phpunit-param array<mixed> $converters
     * @param array<mixed> $converters
     * @param SerializationLoopGuard $loopGuard
     * @param SerializerOptions $options
     */
    public function __construct(array $converters, SerializationLoopGuard $loopGuard, SerializerOptions $options)
    {
        $this->loopGuard = $loopGuard;

        foreach ($converters as $converter) {
            $this->addConverter($converter);
        }

        $this->loopGuard = $loopGuard;
        $this->options = $options;
    }

    public function serialize($object, bool $isRoot = true)
    {
        $this->loopGuard->reset();

        $converter = $this->findConverter($object);

        return $converter
            ? $converter->serialize($object)
            : $object;
    }

    public function hydrate(string $targetClass, $value, bool $isRoot = true)
    {
        $this->loopGuard->reset();

        $converter = $this->findConverter($targetClass);

        return $converter
            ? $converter->hydrate($targetClass, $value)
            : $value;
    }

    /**
     * @param string|object $object
     * @return ValueObjectConverter<ValueObject>|null
     */
    private function findConverter($object): ?ValueObjectConverter
    {
        foreach ($this->converters as $serializer) {
            if ($serializer->accept($object)) {
                return $serializer;
            }
        }

        return null;
    }

    /**
     * @param ValueObjectConverter<ValueObject> $converter
     */
    private function addConverter(ValueObjectConverter $converter): void
    {
        if ($converter instanceof SerializerAware) {
            $converter->setSerializer($this);
        }

        if ($converter instanceof LoopGuardAware) {
            $converter->setLoopGuard($this->loopGuard);
        }

        $this->converters[] = $converter;
    }

    public function getOptions(): SerializerOptions
    {
        return $this->options;
    }

}