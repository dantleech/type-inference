<?php

namespace Phpactor\TypeInference\Domain;

interface SourceCodeLoader
{
    public function loadSourceFor(InferredType $type): SourceCode;
}
