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
use CNastasi\Serializer\Converter\DateTimeImmutableConverter;
use CNastasi\Serializer\Converter\SimpleValueObjectConverter;
use CNastasi\Serializer\SerializationLoopGuard;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Class DefaultSerializerTest
 * @package CNastasi\Serializer
 *
 * @covers \CNastasi\Serializer\DefaultSerializer
 */
class DefaultSerializerIgnoreNullTrueTest extends TestCase
{

    private DefaultSerializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = new DefaultSerializer(
            [
                new DateTimeImmutableConverter(),
                new SimpleValueObjectConverter(),
                new CompositeValueObjectConverter(),
                new CollectionConverter()
            ],
            [],
            new SerializationLoopGuard(),
            new SerializerOptionsDefault(true)
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
        yield 'person' => [
            new Person(
                new Name('John Smith'),
                new Age(33),
                new Address('Hollywood Square', 'Los Angeles'),
                new DateTimeImmutable('2020-10-12T08:53:08+00:00'),
                false,
                null
            ),
            [
                'name' => 'John Smith',
                'age' => 33,
                'address' => [
                    'street' => 'Hollywood Square',
                    'city' => 'Los Angeles'
                ],
                'flag' => false,
                'birthDate' => '2020-10-12T08:53:08+00:00',
            ]
        ];
    }
}
