<?php

namespace DTL\TypeInference\Domain;

final class Offset
{
    private $offset;

    private function __construct(int $offset)
    {
        $this->offset = $offset;
    }

    public static function fromInt(int $offset)
    {
        return new self($offset);
    }
}
