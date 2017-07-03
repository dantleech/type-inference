<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use Microsoft\PhpParser\Node;
use DTL\TypeInference\Domain\Frame as FrameInterface;

final class Frame implements FrameInterface
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

    /**
     * This is not part of the public API.
     */
    public function tolerantNodes(): array
    {
        return $this->nodes;
    }

    public function asDebugMap(): array
    {
        $map = [];
        foreach ($this->nodes as $name => $node) {
            $map[$name] = [
                'node_class' => get_class($node),
                'start' => $node->getStart(),
                'end' => $node->getEndPosition(),
            ];
        }

        return $map;
    }
}
