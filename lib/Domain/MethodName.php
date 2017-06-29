<?php

namespace DTL\TypeInference\Domain;

class MethodName
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): MethodName
    {
        return new self($name);
    }
}
