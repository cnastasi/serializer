<?php

declare(strict_types=1);

namespace CNastasi\Serializer;

use CNastasi\Example\Address;
use CNastasi\Example\Age;
use CNastasi\Example\Classroom;
use CNastasi\Example\Name;
use CNastasi\Example\Person;
use CNastasi\Example\Phone;
use CNastasi\Serializer\Converter\CollectionConverter;
use CNastasi\Serializer\Converter\CompositeValueObjectConverter;
use CNastasi\Serializer\Converter\SimpleValueObjectConverter;
use CNastasi\Serializer\SerializationLoopGuard;
use PHPUnit\Framework\TestCase;

/**
 * Class DefaultSerializerTest
 * @package CNastasi\Serializer
 *
 * @covers \CNastasi\Serializer\DefaultSerializer
 */
class DefaultSerializerTest extends TestCase
{

    private DefaultSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = new DefaultSerializer(
            [
                new SimpleValueObjectConverter(),
                new CompositeValueObjectConverter(),
                new CollectionConverter()
            ],
            new SerializationLoopGuard()
        );
    }

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param object $valueObject
     * @param mixed $expectedResult
     */
    public function shouldSerializeAndHydrate(object $valueObject, $expectedResult): void
    {
        $result = $this->serializer->serialize($valueObject);

        self::assertSame($expectedResult, $result);
        self::assertEquals($valueObject, $this->serializer->hydrate(get_class($valueObject), $result));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProvider(): iterable
    {
        yield 'age' => [new Age(42), 42];

        yield 'age_falsy' => [new Age(0), 0];

        yield 'name' => [new Name('John Smith'), 'John Smith'];

        yield 'address' => [new Address('Broadway st', 'New York'), ['street' => 'Broadway st', 'city' => 'New York']];

        yield 'classroom' => [
            new Classroom(
                [new Name('John Smith'), new Name('Lenny Brown'), new Name('Martha White')]
            ),
            ['John Smith', 'Lenny Brown', 'Martha White']
        ];

        yield 'person' => [
            new Person(
                new Name('John Smith'),
                new Age(33),
                new Address('Hollywood Square', 'Los Angeles'),
                false,
                new Phone('+391234567890')
            ),
            [
                'name' => 'John Smith',
                'age' => 33,
                'address' => [
                    'street' => 'Hollywood Square',
                    'city' => 'Los Angeles'
                ],
                'phone' => '+391234567890',
                'flag' => false
            ]
        ];
    }
}
