<?php

namespace DTL\TypeInference\Domain;

interface Frame
{
    /**
     * Return an associative array of variable names
     * to types.
     */
    public function asDebugMap(): array;

    /**
     * Return all variable names in frame.
     */
    public function names(): array;
}
