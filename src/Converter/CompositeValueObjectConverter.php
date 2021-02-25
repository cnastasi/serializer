<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\Contract\Collection;
use CNastasi\DDD\Contract\CompositeValueObject;
use CNastasi\Serializer\Contract\LoopGuardAware;
use CNastasi\Serializer\Contract\SerializerAware;
use CNastasi\DDD\Contract\ValueObject;
use CNastasi\Serializer\Exception\LoopGuardNotInjectedException;
use CNastasi\Serializer\Exception\NullValueFoundException;
use CNastasi\Serializer\Exception\SerializerNotInjectedException;
use CNastasi\Serializer\Exception\TypeNotFoundException;
use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;
use CNastasi\Serializer\Exception\WrongTypeException;
use CNastasi\Serializer\LoopGuardAwareTrait;
use CNastasi\Serializer\SerializerAwareTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Class CompositeValueObjectConverter
 * @package CNastasi\Serializer\Converter
 *
 * @template I of CompositeValueObject
 * @template O of array<string, mixed>
 *
 * @implements ValueObjectConverter<I, O>
 */
final class CompositeValueObjectConverter implements ValueObjectConverter, SerializerAware, LoopGuardAware
{
    use SerializerAwareTrait;
    use LoopGuardAwareTrait;

    /**
     * @param I $object
     *
     * @return O
     */
    public function serialize($object): array
    {
        if (!$this->serializer) {
            throw new SerializerNotInjectedException();
        }

        if (!$this->loopGuard) {
            throw new LoopGuardNotInjectedException();
        }

        if (!$this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        $data = [];

        $this->loopGuard->addReferenceCount($object);

        $properties = $this->getProperties($object);

        foreach ($properties as $property) {
            $name = $property->getName();

            /** @psalm-suppress MixedAssignment */
            $value = $this->getValue($object, $property);

            $serializedValue = $this->serializer->serialize($value, false);

            if ($this->shouldAddAttribute($serializedValue)) {
                $data[$name] = $serializedValue;
            }
        }

        return $data;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function shouldAddAttribute($value): bool
    {
        return null !== $value || false === $this->serializer->getOptions()->isIgnoreNull();
    }

    /**
     * @psalm-param class-string $targetClass
     * @param string $targetClass
     *
     * @psalm-param O $value
     * @param array $value
     *
     * @psalm-return I
     *
     * @throws ReflectionException
     */
    public function hydrate(string $targetClass, $value): ?CompositeValueObject
    {
        if (!$this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        /** @psalm-var ReflectionClass<I> $class */
        $class = new ReflectionClass($targetClass);

        $parameters = $this->getConstructorParameters($class);

        $args = [];

        foreach ($parameters as $parameter) {
            $type = $this->getReflectionType($targetClass, $parameter);

            /** @var class-string $typeAsString */
            $typeAsString = $type->getName();
            $name = $parameter->getName();

            $argument = $value[$name] ?? null;

            if ($argument === null && !$type->allowsNull()) {
                throw new NullValueFoundException($name, $typeAsString);
            }

            $args[] = $argument
                ? $this->serializer->hydrate($typeAsString, $argument, false)
                : $argument;
        }

        /**
         * @psalm-var I $result
         * @var CompositeValueObject $result
         */
        $result = $class->newInstanceArgs($args);

        return $result;
    }

    /**
     * @param class-string|object $object
     *
     * @return bool
     */
    public function accept($object): bool
    {
        return is_a($object, CompositeValueObject::class, true);
    }

    /**
     * @param I $object
     * @param ReflectionProperty $property
     *
     * @return null|int|string|bool|ValueObject|Collection
     */
    private function getValue(object $object, ReflectionProperty $property)
    {
        $property->setAccessible(true);

        /** @var  null|int|string|bool|ValueObject|Collection $value */
        $value = $property->getValue($object);

        return $value;
    }

    /**
     * @param object $object
     *
     * @return ReflectionProperty[]
     *
     * @throws ReflectionException
     */
    private function getProperties(object $object): array
    {
        $class = $object instanceof ReflectionClass
            ? $object
            : new ReflectionClass($object);

        $parentClass = $class->getParentClass();

        return $parentClass
            ? array_merge($this->getProperties($parentClass), $class->getProperties())
            : $class->getProperties();
    }

    /**
     * @param ReflectionClass<CompositeValueObject> $class
     *
     * @return ReflectionParameter[]
     */
    private function getConstructorParameters(ReflectionClass $class): array
    {
        $constructor = $class->getConstructor();

        return $constructor ? $constructor->getParameters() : [];
    }

    /**
     * @param string $className
     * @param ReflectionParameter $parameter
     *
     * @return ReflectionNamedType
     */
    private function getReflectionType(string $className, ReflectionParameter $parameter): ReflectionNamedType
    {
        /** @var ReflectionNamedType|null $type */
        $type = $parameter->getType();

        if (!$type) {
            throw new TypeNotFoundException($className, $parameter->getName());
        }

        return $type;
    }
}