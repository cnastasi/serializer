<?php

declare(strict_types=1);

namespace CNastasi\Serializer;

use CNastasi\Serializer\Contract\LoopGuardAware;
use CNastasi\Serializer\Contract\SerializerAware;
use CNastasi\DDD\Contract\ValueObject;
use CNastasi\Serializer\Contract\ValueObjectSerializer;
use CNastasi\Serializer\Converter\IdentityConverter;
use CNastasi\Serializer\Converter\SerializableConverter;
use CNastasi\Serializer\Converter\ValueObjectConverter;
use CNastasi\Serializer\Normalizer\IdentityNormalizer;
use CNastasi\Serializer\Normalizer\Normalizer;

/**
 * Class DefaultSerializer
 * @package CNastasi\Serializer
 *
 */
class DefaultSerializer implements ValueObjectSerializer
{
    /** @var ValueObjectConverter<ValueObject>[] $converters */
    private array $converters = [];

    /** @var Normalizer<ValueObject>[] $normalizers */
    private array $normalizers = [];

    private SerializationLoopGuard $loopGuard;

    private SerializerOptions $options;

    /**
     * @param ValueObjectConverter<ValueObject>[] $converters
     * @param Normalizer<ValueObject>[] $normalizers
     * @param SerializationLoopGuard $loopGuard
     * @param SerializerOptions $options
     */
    public function __construct(array $converters, array $normalizers, SerializationLoopGuard $loopGuard, SerializerOptions $options)
    {
        $this->loopGuard = $loopGuard;

        foreach ($converters as $converter) {
            $this->addConverter($converter);
        }

        foreach ($normalizers as $normalizer) {
            $this->addNormalizer($normalizer);
        }

        $this->addConverter(new SerializableConverter());

        $this->loopGuard = $loopGuard;

        $this->options = $options;
    }

    public function serialize($object, bool $isRoot = true)
    {
        $this->loopGuard->reset();

        $converter = $this->findConverter($object);
        $normalizer = $this->findNormalizer($object);

        return $normalizer->normalize($object, $converter->serialize($object));
    }

    public function hydrate(string $targetClass, $value, bool $isRoot = true)
    {
        $this->loopGuard->reset();

        $converter = $this->findConverter($targetClass);

        return $converter->hydrate($targetClass, $value);
    }

    /**
     * @param string|object $object
     * @return ValueObjectConverter<ValueObject>
     */
    private function findConverter($object): ValueObjectConverter
    {
        foreach ($this->converters as $serializer) {
            if ($serializer->accept($object)) {
                return $serializer;
            }
        }

        return new IdentityConverter();
    }

    /**
     * @param object|string $object
     * @return Normalizer<ValueObject>
     */
    private function findNormalizer($object): Normalizer
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->accept($object)) {
                return $normalizer;
            }
        }

        return new IdentityNormalizer();
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

    /**
     * @param Normalizer<ValueObject> $normalizer
     */
    private function addNormalizer(Normalizer $normalizer): void
    {
        $this->normalizers[] = $normalizer;
    }

    public function getOptions(): SerializerOptions
    {
        return $this->options;
    }

}