<?php

namespace DTL\TypeInference\Domain;

final class MessageLog
{
    private $messages = [];

    public function log(string $message)
    {
        $this->messages[] = $message;
    }
}
