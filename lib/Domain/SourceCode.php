<?php

namespace Phpactor\TypeInference\Domain;

final class SourceCode
{
    private $source;

    private function __construct(string $source)
    {
        $this->source = $source;
    }

    public static function fromString(string $source)
    {
        return new self($source);
    }

    public function __toString()
    {
        return $this->source;
    }
}
