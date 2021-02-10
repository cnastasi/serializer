<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\Contract\CompositeValueObject;
use CNastasi\Serializer\Contract\LoopGuardAware;
use CNastasi\Serializer\Contract\SerializerAware;
use CNastasi\DDD\Contract\ValueObject;
use CNastasi\Serializer\Exception\NullValueFoundException;
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
 * @template T of CompositeValueObject
 * @implements ValueObjectConverter<T>
 */
class CompositeValueObjectConverter implements ValueObjectConverter, SerializerAware, LoopGuardAware
{
    use SerializerAwareTrait;
    use LoopGuardAwareTrait;

    /**
     * @inheritDoc
     */
    public function serialize($object)
    {
        if (!$this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        $data = [];

        $this->loopGuard->addReferenceCount($object);

        $properties = $this->getProperties($object);

        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $this->getValue($object, $property);

            $serializedValue = $this->serializer->serialize($value, false);
            if ($this->shouldAddAttribute($serializedValue)) {
                $data[$name] = $serializedValue;
            }
        }

        return $data;
    }

    private function shouldAddAttribute($value)
    {
        return null !== $value || false === $this->serializer->getOptions()->isIgnoreNull();
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function hydrate(string $targetClass, $data): ValueObject
    {
        if (!$this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        if (!is_array($data)) {
            throw new WrongTypeException($data, 'array');
        }

        /** @var ReflectionClass<CompositeValueObject> $class */
        $class = new ReflectionClass($targetClass);

        $parameters = $this->getConstructorParameters($class);

        $args = [];

        foreach ($parameters as $parameter) {
            $type = $this->getReflectionType($targetClass, $parameter);

            /** @var class-string $typeAsString */
            $typeAsString = $type->getName();
            $name = $parameter->getName();

            $value = $data[$name] ?? null;

            if ($value === null && !$type->allowsNull()) {
                throw new NullValueFoundException($name, $typeAsString);
            }

            $args[] = $value
                ? $this->serializer->hydrate($typeAsString, $value, false)
                : $value;
        }

        /**
         * @phpstan-var T $result
         * @var CompositeValueObject
         */
        $result = $class->newInstanceArgs($args);

        return $result;
    }

    /**
     * @param class-string|CompositeValueObject $object
     *
     * @return bool
     */
    public function accept($object): bool
    {
        return is_a($object, CompositeValueObject::class, true);
    }

    /**
     * @param object $object
     * @param ReflectionProperty $property
     *
     * @return mixed
     */
    private function getValue(object $object, ReflectionProperty $property)
    {
        $property->setAccessible(true);

        return $property->getValue($object);
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