<?php

declare(strict_types=1);

namespace CNastasi\Example;

use CNastasi\DDD\Contract\Comparable;
use CNastasi\DDD\Contract\CompositeValueObject;
use CNastasi\DDD\Error\IncomparableObjects;
use CNastasi\DDD\ValueObject\Primitive\DateTime;

/**
 * Class Person
 * @package CNastasi\Example
 *
 * @psalm-immutable
 */
class Person implements CompositeValueObject
{
    private Name $name;
    private Age $age;
    private Address $address;
    private ?Phone $phone;
    private bool $flag;
    private DateTime $birthDate;

    public function __construct(
        Name $name,
        Age $age,
        Address $address,
        DateTime $birthDate,
        bool $flag,
        ?Phone $phone = null
    ) {
        $this->name = $name;
        $this->age = $age;
        $this->address = $address;
        $this->birthDate = $birthDate;
        $this->flag = $flag;
        $this->phone = $phone;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getAge(): Age
    {
        return $this->age;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function isFlag(): bool
    {
        return $this->flag;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function equalsTo(Comparable $item): bool
    {
        if ($item instanceof static) {
            return $item->name->equalsTo($this->name)
                && $item->age->equalsTo($this->age)
                && $item->address->equalsTo($this->address)
                && $item->birthDate->equalsTo($this->birthDate)
                && $item->flag === $this->flag
                && $item->phone->equalsTo($this->phone);
        }

        throw new IncomparableObjects($item, $this);
    }
}
//
// $person = new Person(
//     new Name('Christian Nastasi'),
//     new Age(37),
//     new Address('Via Ponchielli 23', 'Monza')
// );

// $serializer   = new ValueObjectSerializerOld();
// $unserializer = new ValueObjectUnserializer();
//
// $data = $serializer->serialize($person);
//
// $unserializedPerson = $unserializer->unserialize(Person::class, $data);
//
// echo json_encode($data, JSON_PRETTY_PRINT);
//
// print_r($unserializedPerson);
//
/*
 * TODO:
 * Non si può fare un controllo di ricorsione nel caso dell'unserialize, a meno che non si abbiano entità, ed in quel
 * caso si usa l'id (un oggetto di tipo Identifier) per discriminare e quindi fare riferimento alla stessa istanza.
 * In ogni caso si può fare un controllo di profondità e bloccare la ricorsione quando va troppo a fondo.
 *
 * Si dovrebbe avere un oggetto tipo context che definisce questi parametri in maniera dinamica.
 *
 * Inoltre si dovrebbe poter aggiungere i serializzatori e i deserializzatori in maniera dinamica
 */