<?php

namespace Phpactor\TypeInference\Domain;

final class MessageLog
{
    private $messages = [];

    public function log(string $message)
    {
        $this->messages[] = $message;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}
