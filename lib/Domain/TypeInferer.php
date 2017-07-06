<?php

namespace Phpactor\TypeInference\Domain;

use Phpactor\TypeInference\Domain\SourceCode;

interface TypeInferer
{
    public function inferTypeAtOffset(SourceCode $source, Offset $offset): InferredTypeResult;
}
