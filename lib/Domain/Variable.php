<?php

namespace DTL\TypeInference\Domain;

final class Variable
{
    private $name;
    private $type;

    private function __construct(string $name, InferredType $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function fromNameAndType(string $name, InferredType $type)
    {
        return new self($name, $type);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): InferredType
    {
        return $this->type;
    }

}
