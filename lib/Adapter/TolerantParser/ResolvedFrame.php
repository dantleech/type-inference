<?php

namespace DTL\TypeInference\Adapter\TolerantParser;

use DTL\TypeInference\Adapter\TolerantParser\Frame as TolerantFrame;
use DTL\TypeInference\Domain\Frame as FrameInterface;
use DTL\TypeInference\Domain\MessageLog;
use DTL\TypeInference\Domain\SourceCode;
use DTL\TypeInference\Domain\Offset;

class ResolvedFrame implements FrameInterface
{
    private $typeInferrer;
    private $frame;

    public function __construct(TolerantTypeInferer $typeInferrer, TolerantFrame $frame)
    {
        $this->typeInferrer = $typeInferrer;
        $this->frame = $frame;
    }

    public function names(): array
    {
        return $this->frame->names();
    }

    public function asDebugMap(): array
    {
        $map = $this->frame->asDebugMap();
        foreach ($this->frame->tolerantNodes() as $name => $node) {
            $type = $this->typeInferrer->resolveNode(
                new MessageLog(),
                $this->frame,
                $node
            );
            $map[$name]['inferrered_type'] = (string) $type;
        }

        return $map;
    }
}
