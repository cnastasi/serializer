<?php

declare(strict_types=1);

namespace CNastasi\Example;

use CNastasi\DDD\Collection\AbstractCollection;

/**
 * Class Classroom
 * @package CNastasi\Example
 *
 * @extends AbstractCollection<int, Name>
 */
class Classroom extends AbstractCollection
{
    public function getItemType(): string
    {
        return Name::class;
    }
}