<?php

namespace DTL\TypeInference\Domain;

final class DocblockTag
{
    private $name;
    private $value;

    private function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function fromNameAndValue(string $name, $value): DocblockTag
    {
        return new self($name, $value);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value()
    {
        return $this->value;
    }
}
