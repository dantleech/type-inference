<?php

namespace DTL\TypeInference\Domain;

class InferredType
{
    private $type;

    private function __construct(string $type = null)
    {
        $this->type = $type;
    }

    public static function fromString(string $type)
    {
        return new self($type);
    }

    public static function fromParts(array $parts)
    {
        return new self(implode('\\', $parts));
    }

    public static function unknown()
    {
        return new self();
    }
}
