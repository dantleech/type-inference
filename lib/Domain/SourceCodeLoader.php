<?php

namespace DTL\TypeInference\Domain;

use DTL\TypeInference\Domain\InferredType;
use DTL\TypeInference\Domain\SourceCode;

interface SourceCodeLoader
{
    public function loadSourceFor(InferredType $type): SourceCode;
}
