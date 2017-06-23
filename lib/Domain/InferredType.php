<?php

namespace DTL\TypeInference\Domain;

class InferredType
{
    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function fromString(string $type)
    {
        return new self($type);
    }
}
