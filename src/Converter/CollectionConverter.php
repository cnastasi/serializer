<?php

declare(strict_types=1);

namespace CNastasi\Serializer\Converter;

use CNastasi\DDD\Contract\Collection;
use CNastasi\Serializer\Contract\LoopGuardAware;
use CNastasi\Serializer\Contract\SerializerAware;
use CNastasi\DDD\Contract\ValueObject;
use CNastasi\Serializer\Exception\UnableToSerializeException;
use CNastasi\Serializer\Exception\UnacceptableTargetClassException;
use CNastasi\Serializer\Exception\WrongTypeException;
use CNastasi\Serializer\LoopGuardAwareTrait;
use CNastasi\Serializer\SerializerAwareTrait;

/**
 * Class CollectionConverter
 * @package CNastasi\Serializer\Converter
 *
 * @template T of Collection<ValueObject>
 * @implements ValueObjectConverter<T>
 */
class CollectionConverter implements ValueObjectConverter, SerializerAware, LoopGuardAware
{
    use SerializerAwareTrait;
    use LoopGuardAwareTrait;

    /**
     * @phpstan-param T $object
     * @param object $object
     *
     * @return array<mixed>
     */
    public function serialize($object)
    {
        if (!$this->accept($object)) {
            throw new UnableToSerializeException($object);
        }

        $this->loopGuard->addReferenceCount($object);

        $result = [];

        foreach ($object as $item) {
            $result[] = $this->serializer->serialize($item, false);
        }

        return $result;
    }

    public function accept($object): bool
    {
        return is_a($object, Collection::class, true);
    }

    /**
     * @param string $targetClass
     * @param array<mixed>|mixed $value
     *
     * @return ValueObject
     * @phpstan-return T
     *
     * @throws UnacceptableTargetClassException
     * @throws WrongTypeException
     */
    public function hydrate(string $targetClass, $value): ValueObject
    {
        if (!$this->accept($targetClass)) {
            throw new UnacceptableTargetClassException($targetClass);
        }

        if (!is_iterable($value)) {
            throw new WrongTypeException(get_class($value), 'Iterable');
        }

        /** @phpstan-var T $collection */
        $collection = new $targetClass();

        foreach ($value as $item) {
            $type = $collection->getItemType();

            $collection->addItem($this->serializer->hydrate($type, $item));
        }

        return $collection;
    }
}