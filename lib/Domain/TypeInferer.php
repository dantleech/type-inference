<?php

namespace DTL\TypeInference\Domain;

use DTL\TypeInference\Domain\SourceCode;

interface TypeInferer
{
    public function inferTypeAtOffset(SourceCode $source, Offset $offset): InferredType;
}
