<?php

namespace Phpactor\TypeInference\Domain;

use Phpactor\TypeInference\Domain\InferredType;
use Phpactor\TypeInference\Domain\SourceCode;

interface SourceCodeLoader
{
    public function loadSourceFor(InferredType $type): SourceCode;
}
