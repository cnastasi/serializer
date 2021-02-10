<?php

namespace CNastasi\Example;

use CNastasi\DDD\ValueObject\Primitive\Integer;

class Age extends Integer
{
    protected int $min = 0;
    protected int $max = 140;
}