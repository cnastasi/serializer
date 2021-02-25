<?php

declare(strict_types=1);

namespace CNastasi\Serializer;

use CNastasi\Serializer\Contract\LoopGuardAware;
use CNastasi\Serializer\Contract\SerializerAware;
use CNastasi\DDD\Contract\ValueObject;
use CNastasi\DDD\Contract\Collection;
use CNastasi\Serializer\Contract\ValueObjectSerializer;
use CNastasi\Serializer\Converter\IdentityConverter;
use CNastasi\Serializer\Converter\ValueObjectConverter;
use CNastasi\Serializer\Normalizer\IdentityNormalizer;
use CNastasi\Serializer\Normalizer\Normalizer;

/**
 * Class DefaultSerializer
 * @package CNastasi\Serializer
 *
 * @template I of null|int|string|bool|ValueObject|Collection|object
 * @template O of null|int|string|bool|array<mixed>
 *
 * @implements ValueObjectSerializer<I, O>
 */
final class DefaultSerializer implements ValueObjectSerializer
{
    /** @var list<ValueObjectConverter<I, O>> $converters */
    private array $converters = [];

    /** @var list<Normalizer<I, mixed, O>> $normalizers */
    private array $normalizers = [];

    private SerializationLoopGuard $loopGuard;

    private SerializerOptions $options;

    /**
     * @param list<ValueObjectConverter<I, O>> $converters
     * @param list<Normalizer<I, mixed, O>> $normalizers
     * @param SerializationLoopGuard $loopGuard
     * @param SerializerOptions $options
     */
    public function __construct(array $converters, array $normalizers, SerializationLoopGuard $loopGuard, SerializerOptions $options)
    {
        $this->loopGuard = $loopGuard;

        $this->converters = $converters;

        $this->normalizers = $normalizers;

        $this->loopGuard = $loopGuard;

        $this->options = $options;

        $this->initConverters();
    }

    /**
     * @param I $object
     * @param bool $isRoot
     *
     * @return O
     */
    public function serialize($object, bool $isRoot = true)
    {
        $this->loopGuard->reset();

        $converter = $this->findConverter($object);
        $normalizer = $this->findNormalizer($object);

        $data = $converter->serialize($object);

        /** @var O $result */
        $result = $normalizer->normalize($object, $data);

        return $result;
    }

    /**
     * @psalm-param O $value
     * @psalm-return I
     */
    public function hydrate(string $targetClass, $value, bool $isRoot = true)
    {
        $this->loopGuard->reset();

        $converter = $this->findConverter($targetClass);

        /**
         * @psalm-var I $object
         * @var object $object
         */
        $object = $converter->hydrate($targetClass, $value);

        return $object;
    }

    /**
     * @param class-string|I $object
     * @psalm-return ValueObjectConverter<I ,O>|IdentityConverter
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
     * @param mixed $object
     *
     * @return Normalizer<I, O, O>|IdentityNormalizer
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

    private function initConverters(): void
    {
        foreach ($this->converters as $converter) {
            if ($converter instanceof SerializerAware) {
                $converter->setSerializer($this);
            }

            if ($converter instanceof LoopGuardAware) {
                $converter->setLoopGuard($this->loopGuard);
            }
        }
    }

    public function getOptions(): SerializerOptions
    {
        return $this->options;
    }

}