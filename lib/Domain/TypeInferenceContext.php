<?php

namespace DTL\TypeInference\Domain;

final class TypeInferenceContext
{
    private $frame;
    private $messages = [];

    private function __construct(Frame $frame)
    {
        $this->frame = $frame;
    }

    public static function fromFrame(Frame $frame)
    {
        return new self($frame);
    }

    public function log(string $message)
    {
        $this->message[] = $message;
    }

    public function frame(): Frame
    {
        return $this->frame;
    }
}
