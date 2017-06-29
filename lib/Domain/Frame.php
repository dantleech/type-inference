<?php

namespace DTL\TypeInference\Domain;

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
}
