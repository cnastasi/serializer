<?php

declare(strict_types=1);

namespace CNastasi\Example;

use ArrayObject;
use CNastasi\DDD\Contract\Collection;

/**
 * Class Classroom
 * @package CNastasi\Example
 *
 * @implements Collection<Name>
 */
class Classroom implements Collection
{
    /**
     * @var ArrayObject<int, Name> $classroom
     */
    private ArrayObject $classroom;

    /**
     * @param array<Name> $classroom
     */
    public function __construct(array $classroom = [])
    {
        $this->classroom = new ArrayObject();

        foreach ($classroom as $student) {
            $this->addItem($student);
        }
    }

    public function getIterator()
    {
        return $this->classroom;
    }

    public function addItem($name): void
    {
        if ($name instanceof Name) {
            $this->classroom->append($name);
        }
    }

    public function getItemType(): string
    {
        return Name::class;
    }
}