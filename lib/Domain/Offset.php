<?php

namespace Phpactor\TypeInference\Domain;

final class Offset
{
    private $offset;

    private function __construct(int $offset)
    {
        $this->offset = $offset;
    }

    public static function fromInt(int $offset): Offset
    {
        return new self($offset);
    }

    public function asInt()
    {
        return $this->offset;
    }
}
