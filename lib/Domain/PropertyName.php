<?php

namespace DTL\TypeInference\Domain;

class PropertyName
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): PropertyName
    {
        return new self($name);
    }
}
