<?php

declare(strict_types=1);

namespace CNastasi\Serializer;

trait LoopGuardAwareTrait
{
    protected ?SerializationLoopGuard $loopGuard = null;

    public function setLoopGuard(SerializationLoopGuard $loopGuard): void
    {
        $this->loopGuard = $loopGuard;
    }
}