<?php

namespace Phpactor\TypeInference\Domain;

class InferredType
{
    private $type;

    private function __construct(string $type = null)
    {
        $this->type = $type;
    }

    public static function fromString(string $type): InferredType
    {
        if (substr($type, 0, 1) == '\\') {
            $type = substr($type, 1);
        }

        return new self($type);
    }

    public static function fromParts(array $parts): InferredType
    {
        return new self(implode('\\', $parts));
    }

    public static function unknown()
    {
        return new self();
    }

    public function __toString()
    {
        return $this->type ?: '<unknown>';
    }
}
