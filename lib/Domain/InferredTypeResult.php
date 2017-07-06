<?php

namespace Phpactor\TypeInference\Domain;

final class InferredTypeResult
{
    private $type;
    private $frame;
    private $log;

    private function __construct(
        InferredType $type, Frame $frame, MessageLog $log
    )
    {
        $this->type = $type;
        $this->frame = $frame;
        $this->log = $log;
    }

    public static function fromTypeFrameAndMessageLog(InferredType $type, Frame $frame, MessageLog $log)
    {
        return new self($type, $frame, $log);
    }

    public function type(): InferredType
    {
        return $this->type;
    }

    public function frame(): Frame
    {
        return $this->frame;
    }

    public function log(): MessageLog
    {
        return $this->log;
    }
}
