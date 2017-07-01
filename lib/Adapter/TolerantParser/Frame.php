<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Node;

final class Frame
{
    private $nodes = [];

    public function set(string $name, Node $node)
    {
        $this->nodes[$name] = $node;
    }

    public function get(string $name)
    {
        return $this->nodes[$name] ?? null;
    }

    public function names(): array
    {
        return array_keys($this->nodes);
    }
}
