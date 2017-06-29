<?php

namespace DTL\TypeInference\Domain;

use DTL\TypeInference\Domain\InferredType;

final class Frame
{
    private $variables;

    public function set(Variable $variable)
    {
        $this->variables[$variable->name()] = $variable;
    }

    public function get(string $name)
    {
        if (false === isset($this->variables[$name])) {
            return;
        }

        return $this->variables[$name];
    }

    public function getOrUnknown(string $name): Variable
    {
        return $this->get($name) ?: InferredType::unknown();
    }
}
